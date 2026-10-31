<?php

namespace App\Modules\Payment\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Modules\Payment\Models\LichSuPayos;

use App\Models\LichSuGiaoDich;
use App\Models\User;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request as FakeRequest;

use Illuminate\Support\Facades\DB;

class PaymentWebhookController extends Controller
{
    public function webhook(Request $request)
    {
        Log::info('[PayOS webhook]', $request->all());

        $ma_don = $request->input('data.orderCode');

        $code = $request->input('code');
        if (!$ma_don) {
            return response()->json([
                "code" => -1,
                "message" => "Thiếu orderCode"
            ], 400);
        }
        Log::info('PayOS webhook', [
            'ma_don' => $ma_don
        ]);
        DB::beginTransaction();

        try {

            $don_hang = LichSuPayos::where('ma_don', $ma_don)
                ->lockForUpdate()
                ->first();

            if (!$don_hang) {
                DB::rollBack();

                return response()->json([
                    "code" => 0,
                    "message" => "Không tìm thấy đơn hàng"
                ], 404);
            }


            if ($don_hang->trang_thai === 'Thành công') {
                DB::rollBack();

                return response()->json([
                    "code" => 0,
                    "message" => "Đã xử lý trước đó"
                ]);
            }


            if ($code === '00') {

                $adminTrungGian = User::lockForUpdate()->find($this->getTrungGianId());
                $nguoiNap = User::lockForUpdate()->find($don_hang->admin_id);

                if (!$adminTrungGian || !$nguoiNap) {
                    DB::rollBack();

                    return response()->json([
                        "code" => -1,
                        "message" => "Không tìm thấy user"
                    ]);
                }

                $soTien = $don_hang->so_tien;


                $adminTrungGian->decrement('vi_tien', $soTien);


                $nguoiNap->increment('vi_tien_mua_hang', $soTien);

                $adminTrungGian->refresh();
                $nguoiNap->refresh();

// người trung gian
                LichSuGiaoDich::create([
                    'sender_id' => $adminTrungGian->id,
                    'receiver_id' => $nguoiNap->id,
                    'amount' => $soTien,
                    'note' => 'Nạp tiền PayOS ',
                    'admin_id' => $adminTrungGian->id,
                    'surplus' => $adminTrungGian->vi_tien,
                    'surplus_vi_mua'=>$adminTrungGian->vi_tien_mua_hang,
                    'surplus_vi_mua_befor'=>$adminTrungGian->vi_tien_mua_hang,
                    'type' => '1',
                    'company_id' => $adminTrungGian->company_id,
                ]);

// người nạp tiền
                LichSuGiaoDich::create([
                    'sender_id' => $adminTrungGian->id,
                    'receiver_id' => $nguoiNap->id,
                    'amount' => $soTien,
                    'note' => 'Nạp tiền PayOS vào ví mua hàng ',
                    'admin_id' => $nguoiNap->id,
                    'surplus' => $nguoiNap->vi_tien,
                    'surplus_vi_mua'=>$nguoiNap->vi_tien_mua_hang,
                    'surplus_vi_mua_befor'=>$nguoiNap->vi_tien_mua_hang - $soTien,
                    'type' => '0',
                    'company_id' => $nguoiNap->company_id,
                ]);

                    $this->processOrderFromPayos($don_hang);

                $don_hang->update([
                    'trang_thai' => 'Thành công',
                    'cancel' => 0,

                    'accountNumber' => $request->input('data.accountNumber'),
                    'reference' => $request->input('data.reference'),
                    'transactionDateTime' => $request->input('data.transactionDateTime'),
                    'virtualAccountNumber' => $request->input('data.virtualAccountNumber'),
                    'counterAccountBankId' => $request->input('data.counterAccountBankId'),
                    'counterAccountBankName' => $request->input('data.counterAccountBankName'),
                    'counterAccountName' => $request->input('data.counterAccountName'),
                    'counterAccountNumber' => $request->input('data.counterAccountNumber'),
                    'virtualAccountName' => $request->input('data.virtualAccountName'),
                    'currency' => $request->input('data.currency'),
                ]);
            }


//            if ($status === 'CANCELLED') {
//                $don_hang->update([
//                    'trang_thai' => 'Đã hủy',
//                    'cancel'     => 1
//                ]);
//            }

            DB::commit();

            return response()->json([
                "code" => 0,
                "message" => "Thành công"
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('[PayOS webhook] lỗi: ' . $e->getMessage());

            return response()->json([
                "code" => -1,
                "message" =>  $e->getMessage()
            ], 500);
        }
    }

    private function getTrungGianId(): int
    {
        return (int)config('payment.trung_gian_id', env('PAYOS_TRUNG_GIAN_ID', 22205));
    }


    public function processOrderFromPayos(LichSuPayos $payos): void
    {
        if (empty($payos->product_json)) {
            return;
        }

        $data = json_decode($payos->product_json, true);

        if (!$data) {
            throw new \Exception('Dữ liệu product_json không hợp lệ');
        }

        $user = User::find($payos->admin_id);

        if (!$user) {
            throw new \Exception('Không tìm thấy người mua');
        }

        $product = Product::find($data['product_id']);

        if (!$product) {
            throw new \Exception('Không tìm thấy sản phẩm');
        }

        // Login giả để OrderService hiện tại hoạt động
        auth()->loginUsingId($user->id);

        $fakeRequest = new FakeRequest();
        $fakeRequest->replace($data);

        $orderService = app(OrderService::class);

        if (!empty($data['child_ids'])) {

            $order = $orderService->muaTuChon(
                $fakeRequest,
                $product
            );

        } else {

            $order = $orderService->muaGoi(
                $fakeRequest,
                $product
            );
        }

        $payos->update([
            'order_id' => $order->id,
        ]);
    }

}