<?php

namespace App\Modules\Finance\Services;

use App\Models\LichSuGiaoDich;
use App\Models\User;
use App\Modules\Finance\Exceptions\FinanceException;
use Illuminate\Support\Facades\DB;

/**
 * Chuyển điểm nội bộ từ ví chính sang ví của người khác.
 */
class TransferService
{
    private const WALLETS = ['vi_tien', 'vi_tien_mua_hang'];

    public function lookupRecipient(User $sender, string $query): ?User
    {
        return User::where(function ($q) use ($query) {
            $q->where('tel', $query)
                ->orWhere('id', is_numeric($query) ? $query : -1)
                ->orWhere('email', $query);
        })
            ->where('id', '!=', $sender->id)
            ->select('id', 'name', 'tel', 'email')
            ->first();
    }

    /** @throws FinanceException */
    public function transfer(User $sender, string $recipientKey, float $amount, string $wallet): User
    {
        if (!in_array($wallet, self::WALLETS, true)) {
            throw new FinanceException('Ví nhận không hợp lệ.');
        }

        $recipient = $this->lookupRecipient($sender, $recipientKey);

        if (!$recipient) {
            throw new FinanceException('Không tìm thấy người dùng.');
        }

        return DB::transaction(function () use ($sender, $recipient, $amount, $wallet) {
            // Khoá cả 2 ví theo thứ tự id để tránh deadlock và tránh tiêu trùng số dư
            $locked = User::lockForUpdate()
                ->whereIn('id', [$sender->id, $recipient->id])
                ->orderBy('id')
                ->get()
                ->keyBy('id');

            $from = $locked->get($sender->id);
            $to = $locked->get($recipient->id);

            if ($from->vi_tien < $amount) {
                throw new FinanceException('Số dư ví chính không đủ. Hiện có ' . number_format($from->vi_tien, 0, ',', '.') . 'đ.');
            }

            $this->debit($from, $to, $amount);
            $this->credit($from, $to, $amount, $wallet);

            return $to;
        });
    }

    private function debit(User $from, User $to, float $amount): void
    {
        $before = $from->vi_tien;
        $from->decrement('vi_tien', $amount);

        LichSuGiaoDich::create([
            'admin_id'       => $from->id,
            'receiver_id'    => $to->id,
            'surplus_before' => $before,
            'amount'         => $amount,
            'surplus'        => $before - $amount,
            'note'           => 'Chuyển ' . number_format($amount) . 'đ tới ' . $to->name,
            'type'           => 5,
        ]);
    }

    private function credit(User $from, User $to, float $amount, string $wallet): void
    {
        $before = $to->$wallet;
        $to->increment($wallet, $amount);
        $isMainWallet = $wallet === 'vi_tien';

        LichSuGiaoDich::create([
            'admin_id'             => $to->id,
            'sender_id'            => $from->id,
            'amount'               => $amount,
            'note'                 => 'Nhận từ ' . $from->name,
            'type'                 => 5,
            'surplus_before'       => $isMainWallet ? $before : null,
            'surplus'              => $isMainWallet ? $before + $amount : null,
            'surplus_vi_mua_befor' => $isMainWallet ? null : $before,
            'surplus_vi_mua'       => $isMainWallet ? null : $before + $amount,
        ]);
    }
}
