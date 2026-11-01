<?php

namespace App\Modules\Finance\Services;

use App\Models\LichSuGiaoDich;
use App\Models\NganHangBank;
use App\Models\User;
use App\Models\YeuCauRutTien;
use App\Modules\Finance\Exceptions\FinanceException;
use App\Modules\Finance\Repositories\WithdrawalRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WithdrawalService
{
    /** Ngân hàng không cần chọn chi nhánh */
    private const NO_BRANCH_BANK = 'Ngân hàng Á Châu';

    public function __construct(private readonly WithdrawalRepository $withdrawals)
    {
    }

    public function listForAdmin(array $filters): LengthAwarePaginator
    {
        return $this->withdrawals->paginateForAdmin($filters);
    }

    public function exportItems(array $filters): Collection
    {
        return $this->withdrawals->allForExport($filters);
    }

    /** Dữ liệu trang rút tiền của user */
    public function userPageData(User $user): array
    {
        $userBankCityId = null;
        $userTongNganHangName = null;
        $branch = $user->ngan_hang_id ? NganHangBank::find($user->ngan_hang_id) : null;

        if ($branch) {
            $userBankCityId = $branch->thanh_pho_id;
            $userTongNganHangName = $branch->tong_ngan_hang_id
                ? DB::table('tong_ngan_hang')->where('id', $branch->tong_ngan_hang_id)->value('name')
                : null;
        }

        return [
            'items'                => $this->withdrawals->paginateOfUser($user->id),
            'thanhPho'             => DB::table('thanh_pho_banks')->get(),
            'nganHangs'            => DB::table('ngan_hangs')->get(),
            'tenNganHangs'         => DB::table('tong_ngan_hang')->get(),
            'userBankCityId'       => $userBankCityId,
            'userTongNganHangName' => $userTongNganHangName,
        ];
    }

    /**
     * User gửi yêu cầu rút tiền: trừ ví ngay, admin từ chối thì hoàn lại.
     *
     * @throws FinanceException
     */
    public function request(User $user, array $data): void
    {
        // khóa theo user trong 10 giây
        $lock = Cache::lock('withdraw_lock_' . $user->id, 10);

        if (!$lock->get()) {
            throw new FinanceException('Yêu cầu rút tiền đang được xử lý, vui lòng đợi vài giây.');
        }

        try {
            if (!hash_equals((string) $user->pin, (string) $data['pin'])) {
                throw new FinanceException('vui lòng nhập lại mã pin.');
            }

            if ($data['ngan_hang'] !== self::NO_BRANCH_BANK && empty($data['ngan_hang_id'])) {
                throw new FinanceException('Vui lòng chọn chi nhánh ngân hàng.');
            }

            DB::transaction(fn () => $this->createRequest($user->id, $data));
        } finally {
            optional($lock)->release();
        }
    }

    public function approve(YeuCauRutTien $request): void
    {
        DB::transaction(function () use ($request) {
            $this->lockPending($request)->update(['status' => 1]);
        });
    }

    /** Từ chối: cộng tiền lại và ghi lịch sử */
    public function reject(YeuCauRutTien $request): void
    {
        DB::transaction(function () use ($request) {
            $locked = $this->lockPending($request);
            $user = User::lockForUpdate()->find($locked->user_id);

            if (!$user) {
                throw new FinanceException('Khong tim thay thanh vien.');
            }

            $viTruoc = $user->vi_tien;
            $user->increment('vi_tien', $locked->so_tien);

            LichSuGiaoDich::create([
                'admin_id'             => $user->id,
                'sender_id'            => $user->id,
                'receiver_id'          => $user->id,
                'surplus_before'       => $viTruoc,
                'amount'               => $locked->so_tien,
                'surplus'              => $viTruoc + $locked->so_tien,
                'surplus_vi_mua'       => $user->vi_tien_mua_hang,
                'surplus_vi_mua_befor' => $user->vi_tien_mua_hang,
                'note'                 => 'Hoan lai tien do tu choi rut tien ve ' . $locked->ngan_hang . ' - ' . $locked->stk,
                'type'                 => 5,
            ]);

            $locked->update(['status' => 2]);
        });
    }

    public function delete(YeuCauRutTien $request): void
    {
        $this->withdrawals->delete($request);
    }

    public function recalculateFees(): void
    {
        foreach ($this->withdrawals->all() as $item) {
            $item->{'10_phan_tram_so_tien'} = ($item->so_tien * 10) / 100;
            $item->{'1_5_phan_tram_so_tien'} = ($item->so_tien * 1.5) / 100;
            $item->save();
        }
    }

    private function lockPending(YeuCauRutTien $request): YeuCauRutTien
    {
        $locked = $this->withdrawals->lockedPending($request->id);

        if (!$locked) {
            throw new FinanceException('Yeu cau nay da duoc xu ly truoc do.');
        }

        return $locked;
    }

    private function createRequest(int $userId, array $data): void
    {
        $user = User::lockForUpdate()->find($userId);
        $soTien = (float) $data['so_tien'];

        if ($user->vi_tien < $soTien) {
            throw new FinanceException('Số dư ví không đủ.');
        }

        $viTruoc = $user->vi_tien;
        $user->decrement('vi_tien', $soTien);

        $this->withdrawals->create([
            'user_id'          => $user->id,
            'so_tien'          => $data['so_tien'],
            'so_tien_sau_thue' => $data['so_tien'] * 11.5 / 100,
            'cast_now'         => $user->vi_tien, // số dư lúc tạo để admin tham khảo
            'ngan_hang'        => $data['ngan_hang'],
            'stk'              => $data['stk'],
            'chu_tk'           => $data['chu_tk'],
            'status'           => 0, // 0 = chờ duyệt
            'note'             => $data['note'] ?? null,
            'cccd'             => $data['cccd'],
            'ngan_hang_id'     => $data['ngan_hang_id'] ?? null,
            'ACB_Checkbox'     => ($data['ACB_Checkbox'] ?? null) === 'yes',
        ]);

        LichSuGiaoDich::create([
            'admin_id'       => $user->id,
            'sender_id'      => $user->id,
            'receiver_id'    => $user->id,
            'surplus_before' => $viTruoc,
            'amount'         => $soTien,
            'surplus'        => $viTruoc - $soTien,
            'note'           => 'Tao yeu cau rut tien',
            'type'           => 3,
        ]);
    }
}
