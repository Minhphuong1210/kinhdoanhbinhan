<?php

namespace App\Modules\Payment\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Payment\Services\PaymentHistoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentHistoryController extends Controller
{
    public function __construct(private readonly PaymentHistoryService $historyService)
    {
    }

    /**
     * GET /admin/payment-history
     */
    public function index(Request $request)
    {
        [$items, $tongTien] = $this->historyService->list(
            $request->only(['trang_thai', 'tu_ngay', 'den_ngay', 'description']),
            $request->user()
        );

        return view('Payment::Admin.payment-history.index', compact('items', 'tongTien'));
    }

    /**
     * GET /payos/cancel — PayOS redirect về khi khách bấm Huỷ
     */
    public function cancelPayment(Request $request)
    {
        $orderCode = $request->input('orderCode');

        if (!$orderCode || !is_numeric($orderCode)) {
            return $this->backToList('error', 'Yêu cầu không hợp lệ.');
        }

        $this->historyService->cancel($orderCode, $request->input('id'));

        return $this->backToList('warning', 'Bạn đã huỷ giao dịch nạp tiền.');
    }

    /**
     * GET /payos/succes — PayOS redirect về khi thanh toán thành công
     */
    public function succesPayment(Request $request)
    {
        $orderCode = $request->input('orderCode');
        Log::info('[PayOS] succesPayment ma_don=' . $orderCode);

        if (!$orderCode || !is_numeric($orderCode)) {
            return $this->backToList('error', 'Yêu cầu không hợp lệ.');
        }

        if (!$this->historyService->markReturned($orderCode, $request->input('id'))) {
            Log::error('[PayOS] succesPayment không tìm thấy ma_don=' . $orderCode);

            return $this->backToList('error', 'Không tìm thấy giao dịch nạp tiền.');
        }

        return $this->backToList('success', 'Nạp tiền thành công');
    }

    /**
     * GET /admin/payment-history/cap-nhat — đồng bộ thủ công trạng thái từ PayOS
     */
    public function capNhatTrangThai()
    {
        $updated = $this->historyService->syncPending();

        return back()->with('success', "Đồng bộ xong. Cập nhật {$updated} đơn thành công.");
    }

    private function backToList(string $type, string $message)
    {
        return redirect()->route('admin.payment-history.index')->with($type, $message);
    }
}
