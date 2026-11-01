<?php

namespace App\Modules\Finance\Controllers\Admin;

use App\Exports\YeuCauRutTienExport;
use App\Http\Controllers\Controller;
use App\Models\YeuCauRutTien;
use App\Modules\Finance\Exceptions\FinanceException;
use App\Modules\Finance\Services\WithdrawalService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class YeuCauRutTienController extends Controller
{
    public function __construct(private readonly WithdrawalService $withdrawalService)
    {
    }

    public function index(Request $request)
    {
        $items = $this->withdrawalService->listForAdmin($request->only(['status', 'date_from', 'date_to']));

        return view('Finance::Admin.yeu-cau-rut-tien.index', compact('items'));
    }

    public function update(Request $request, YeuCauRutTien $yeuCauRutTien)
    {
        $request->validate(['action' => 'required|in:approve,reject']);

        $name = $yeuCauRutTien->user?->name ?? '?';

        try {
            if ($request->action === 'approve') {
                $this->withdrawalService->approve($yeuCauRutTien);

                return back()->with('success', 'Đã duyệt yêu cầu rút tiền của ' . $name);
            }

            $this->withdrawalService->reject($yeuCauRutTien);
        } catch (FinanceException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return back()->with(
            'success',
            'Đã từ chối và hoàn lại ' . number_format($yeuCauRutTien->so_tien, 0, ',', '.') . 'đ cho ' . $name
        );
    }

    public function destroy(YeuCauRutTien $yeuCauRutTien)
    {
        $this->withdrawalService->delete($yeuCauRutTien);

        return back()->with('success', 'Xoa thanh cong!');
    }

    /** Tính lại 2 cột phí 10% và 1,5% cho toàn bộ yêu cầu */
    public function recalculateFees()
    {
        $this->withdrawalService->recalculateFees();
    }

    public function exportExcel(Request $request)
    {
        $items = $this->withdrawalService->exportItems($request->only(['status', 'date_from', 'date_to']));
        $from = $request->date_from ? '_' . $request->date_from . '_' . ($request->date_to ?: now()->toDateString()) : '';

        return Excel::download(new YeuCauRutTienExport($items), 'Yeu-cau-rut-tien' . $from . '.xlsx');
    }
}
