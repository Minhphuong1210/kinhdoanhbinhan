<?php

namespace App\Modules\Payment\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LichSuGiaoDich;
use App\Models\User;
use App\Modules\Payment\Models\LichSuPayos;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
class PaymentHistoryController extends Controller
{
    // ----------------------------------------------------------------
    //  Admin list
    // ----------------------------------------------------------------

    /**
     * GET /admin/payment-history
     */
    public function index(Request $request)
    {
        $query = LichSuPayos::with('nguoiNap')->latest();

        // Chỉ super-admin thấy tất cả, user thường thấy của mình
        if (!$this->isSuperAdmin()) {
            $query->where('admin_id', auth()->id());
        }

        // Filter trạng thái
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Filter ngày
        if ($request->filled('tu_ngay')) {
            $query->whereDate('created_at', '>=', $request->tu_ngay);
        }
        if ($request->filled('den_ngay')) {
            $query->whereDate('created_at', '<=', $request->den_ngay);
        }

        // Filter nội dung
        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->description . '%');
        }

        $tongTien = (clone $query)->where('trang_thai', 'Thành công')->sum('so_tien');
        $items    = $query->paginate(20)->withQueryString();

        return view('Payment::Admin.payment-history.index', compact('items', 'tongTien'));
    }

    // ----------------------------------------------------------------
    //  PayOS callbacks (public — không cần auth vì PayOS redirect)
    // ----------------------------------------------------------------

    /**
     * GET /payos/cancel
     * PayOS redirect về đây khi khách bấm Huỷ
     */
    public function cancelPayment(Request $request)
    {
        $madon = $request->input('orderCode');

        // Bắt buộc phải có orderCode hợp lệ
        if (!$madon || !is_numeric($madon)) {
            return redirect()->route('admin.payment-history.index')
                             ->with('error', 'Yêu cầu không hợp lệ.');
        }

        $record = LichSuPayos::where('ma_don', $madon)->first();

        // Chỉ cập nhật nếu tìm thấy và chưa hoàn thành
        if ($record && !in_array($record->trang_thai, ['Thành công', 'Đã hủy'])) {
            $record->update([
                'payment_id' => $request->input('id'),
                'cancel'     => 1,
                'trang_thai' => 'Đã hủy',
            ]);
        }

        return redirect()->route('admin.payment-history.index')
                         ->with('warning', 'Bạn đã huỷ giao dịch nạp tiền.');
    }

    /**
     * GET /payos/succes
     * PayOS redirect về đây khi thanh toán thành công
     */
    public function succesPayment(Request $request)
    {
        $madon = $request->input('orderCode');
        Log::info('[PayOS] succesPayment ma_don=' . $madon );
        // Validate orderCode
        if (!$madon || !is_numeric($madon)) {
            return redirect()->route('admin.payment-history.index')
                             ->with('error', 'Yêu cầu không hợp lệ.');
        }

        $record = LichSuPayos::where('ma_don', $madon)->first();






        DB::beginTransaction();
        try {



            // Cập nhật trạng thái đơn payos
            $record->update([
                'payment_id' => $request->input('id'),

            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[PayOS] succesPayment error ma_don=' . $madon . ': ' . $e->getMessage());

            return redirect()->route('admin.payment-history.index')
                             ->with('error', 'Thanh toán ghi nhận lỗi: ' . $e->getMessage());
        }

        $order = Order::where('id',$record->order_id)->first();

        if($order){
            return redirect()->route('admin.thank-you', ['orderId' => $order->ma_don_hang])
                ->with('success', 'Đặt mua thành công! Mã đơn: ' . $order->ma_don_hang);
        }else{
            return redirect()->route('admin.payment-history.index')
                ->with('success', 'Nạp tiền thành công ' );
        }



    }

    // ----------------------------------------------------------------
    //  Thủ công: đồng bộ trạng thái từ PayOS API
    // ----------------------------------------------------------------

    /**
     * GET /admin/payment-history/cap-nhat
     * Kiểm tra lại những đơn "Đang xử lý" từ hôm qua đến hôm nay.
     */
    public function capNhatTrangThai(Request $request)
    {
        $lichSuChuaXong = LichSuPayos::where('trang_thai', 'Đang xử lý')
            ->whereBetween('created_at', [Carbon::yesterday(), Carbon::now()])
            ->orderByDesc('created_at')
            ->get();

        $updated = 0;

        foreach ($lichSuChuaXong as $item) {
            try {
                $payosStatus = $this->verifyPayosPayment($item->ma_don);

                if ($payosStatus === 'PAID') {
                    DB::beginTransaction();

                    // 🔒 Lock row để chắc chắn chưa được xử lý
                    $locked = LichSuPayos::where('id', $item->id)
                                          ->where('trang_thai', 'Đang xử lý')
                                          ->lockForUpdate()
                                          ->first();

                    if (!$locked) {
                        DB::rollBack();
                        continue; // Đã được xử lý bởi request khác
                    }

                    $adminTrungGian = User::lockForUpdate()->find($this->getTrungGianId());
                    $nguoiNap       = User::lockForUpdate()->find($item->admin_id);

                    if (!$adminTrungGian || !$nguoiNap) {
                        DB::rollBack();
                        continue;
                    }

                    $adminTrungGian->decrement('vi_tien', $item->so_tien);
                    $nguoiNap->increment('vi_tien', $item->so_tien);

                    $adminTrungGian->refresh();
                    $nguoiNap->refresh();

                    LichSuGiaoDich::create([
                        'sender_id'   => $adminTrungGian->id,
                        'receiver_id' => $nguoiNap->id,
                        'amount'      => $item->so_tien,
                        'note'        => 'Nạp tiền PayOS (sync)',
                        'admin_id'    => $adminTrungGian->id,
                        'surplus'     => $adminTrungGian->vi_tien,
                        'type'        => '1',
                        'company_id'  => $adminTrungGian->company_id,
                    ]);

                    LichSuGiaoDich::create([
                        'sender_id'   => $adminTrungGian->id,
                        'receiver_id' => $nguoiNap->id,
                        'amount'      => $item->so_tien,
                        'note'        => 'Nạp tiền PayOS (sync)',
                        'admin_id'    => $nguoiNap->id,
                        'surplus'     => $nguoiNap->vi_tien,
                        'type'        => '0',
                        'company_id'  => $nguoiNap->company_id,
                    ]);

                    $locked->update([
                        'trang_thai' => 'Thành công',
                        'cancel'     => 0,
                    ]);

                    DB::commit();
                    $updated++;
                }

                if ($payosStatus === 'CANCELLED') {
                    $item->update(['trang_thai' => 'Đã hủy', 'cancel' => 1]);
                }

            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('[PayOS] capNhat error ma_don=' . $item->ma_don . ': ' . $e->getMessage());
            }
        }

        return back()->with('success', "Đồng bộ xong. Cập nhật {$updated} đơn thành công.");
    }

    // ----------------------------------------------------------------
    //  Private helpers
    // ----------------------------------------------------------------

    private function isSuperAdmin(): bool
    {
        return (int) (auth()->user()?->super_admin ?? 0) === 1;
    }

    /**
     * ID tài khoản trung gian — đặt trong config hoặc env, KHÔNG hardcode.
     */
    private function getTrungGianId(): int
    {
        return (int) config('payment.trung_gian_id', env('PAYOS_TRUNG_GIAN_ID', 22205));
    }

    /**
     * Gọi PayOS API để xác minh trạng thái thực của đơn.
     * Trả về: 'PAID' | 'CANCELLED' | 'PENDING' | 'ERROR'
     */
    private function verifyPayosPayment(int|string $madon): string
    {
        try {
            $client   = new Client(['timeout' => 10]);
            $response = $client->get("https://api-merchant.payos.vn/v2/payment-requests/{$madon}", [
                'headers' => [
                    'x-client-id'  => config('services.payos.client_id', env('PAYOS_CLIENT_ID')),
                    'x-api-key'    => config('services.payos.api_key', env('PAYOS_API_KEY')),
                    'Content-Type' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if (($data['code'] ?? '') !== '00') {
                Log::warning('[PayOS] verifyPayment: code không hợp lệ ma_don=' . $madon, $data);
                return 'ERROR';
            }

            return $data['data']['status'] ?? 'PENDING';

        } catch (\Throwable $e) {
            Log::error('[PayOS] verifyPayment exception ma_don=' . $madon . ': ' . $e->getMessage());
            return 'ERROR';
        }
    }
}
