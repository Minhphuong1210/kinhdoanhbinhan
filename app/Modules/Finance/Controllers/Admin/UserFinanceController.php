<?php

namespace App\Modules\Finance\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Exceptions\FinanceException;
use App\Modules\Finance\Repositories\TransactionRepository;
use App\Modules\Finance\Services\IncomeService;
use App\Modules\Finance\Services\TransferService;
use App\Modules\Finance\Services\WithdrawalService;
use Illuminate\Http\Request;

/**
 * Tài chính cá nhân: giao dịch, rút tiền, chuyển điểm, thu nhập.
 */
class UserFinanceController extends Controller
{
    public function __construct(
        private readonly TransactionRepository $transactions,
        private readonly WithdrawalService $withdrawalService,
        private readonly TransferService $transferService,
        private readonly IncomeService $incomeService
    ) {
    }

    /**
     * Lịch sử giao dịch của user hiện tại
     */
    public function transactions()
    {
        $user = auth()->user();
        $items = $this->transactions->paginateOfUser($user->id);

        return view('Core::Admin.finance.transactions', compact('items', 'user'));
    }

    /**
     * Danh sách yêu cầu rút tiền của user hiện tại
     */
    public function withdrawals()
    {
        $user = auth()->user();

        return view('Core::Admin.finance.withdrawals', ['user' => $user] + $this->withdrawalService->userPageData($user));
    }

    /**
     * Gửi yêu cầu rút tiền mới
     */
    public function requestWithdrawal(Request $request)
    {
        $data = $request->validate([
            'so_tien'      => 'required|numeric|min:10000',
            'ngan_hang'    => 'required|max:100',
            'stk'          => 'required|max:50',
            'chu_tk'       => 'required|max:100',
            'cccd'         => 'required|max:100',
            'pin'          => 'required',
            'note'         => 'nullable|string|max:500',
            'ngan_hang_id' => 'nullable',
            'ACB_Checkbox' => 'nullable',
        ]);

        try {
            $this->withdrawalService->request(auth()->user(), $data);
        } catch (FinanceException $e) {
            return back()->with('error', $e->getMessage())->withInput($request->except('pin'));
        }

        return back()->with('success', 'Đã gửi yêu cầu rút tiền thành công.');
    }

    public function income()
    {
        return view('Core::Admin.income.index', $this->incomeService->monthlyData(auth()->user()));
    }

    // --------------------------------------------------------
    //  Chuyển điểm nội bộ
    // --------------------------------------------------------

    public function lookupUser(Request $request)
    {
        $query = trim((string) $request->q);

        if (strlen($query) < 3) {
            return response()->json(['found' => false]);
        }

        $user = $this->transferService->lookupRecipient(auth()->user(), $query);

        if (!$user) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'id'    => $user->id,
            'name'  => $user->name,
            'tel'   => $user->tel,
        ]);
    }

    public function transferForm()
    {
        $user = auth()->user();

        return view('Core::Admin.finance.transfer', compact('user'));
    }

    public function processTransfer(Request $request)
    {
        $data = $request->validate([
            'nguoi_nhan' => 'required|string|max:255',
            'so_tien'    => 'required|numeric|min:1000',
            'loai_vi'    => 'required|in:vi_tien,vi_tien_mua_hang',
        ], [
            'nguoi_nhan.required' => 'Vui lòng nhập SĐT / ID / Email người nhận.',
            'so_tien.min'         => 'Số tiền tối thiểu là 1.000đ.',
            'loai_vi.required'    => 'Vui lòng chọn ví nhận.',
        ]);

        $soTien = (float) $data['so_tien'];

        try {
            $recipient = $this->transferService->transfer(auth()->user(), $data['nguoi_nhan'], $soTien, $data['loai_vi']);
        } catch (FinanceException $e) {
            return back()->withErrors(['so_tien' => $e->getMessage()])->withInput();
        }

        return back()->with('success', 'Đã chuyển ' . number_format($soTien, 0, ',', '.') . 'đ tới ' . $recipient->name);
    }
}
