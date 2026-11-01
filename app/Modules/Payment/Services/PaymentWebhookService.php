<?php

namespace App\Modules\Payment\Services;

use App\Models\LichSuGiaoDich;
use App\Models\Product;
use App\Models\User;
use App\Modules\Cart\Services\CartService;
use App\Modules\Payment\Exceptions\PaymentException;
use App\Modules\Payment\Models\LichSuPayos;
use App\Modules\Payment\Repositories\PaymentRepository;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Xử lý khi PayOS báo đã nhận tiền: cộng ví mua hàng rồi tạo đơn từ product_json.
 */
class PaymentWebhookService
{
    public const RESULT_PROCESSED         = 'processed';
    public const RESULT_ALREADY_PROCESSED = 'already_processed';
    public const RESULT_NOT_FOUND         = 'not_found';
    public const RESULT_IGNORED           = 'ignored';

    private const BANK_FIELDS = [
        'accountNumber', 'reference', 'transactionDateTime', 'virtualAccountNumber',
        'counterAccountBankId', 'counterAccountBankName', 'counterAccountName',
        'counterAccountNumber', 'virtualAccountName', 'currency',
    ];

    public function __construct(
        private readonly OrderService $orderService,
        private readonly PaymentRepository $payments,
        private readonly CartService $cartService
    ) {
    }

    /**
     * @param array $data phần `data` PayOS gửi lên
     */
    public function handle(int|string $orderCode, ?string $code, array $data): string
    {
        return DB::transaction(function () use ($orderCode, $code, $data) {
            $payment = $this->payments->lockByOrderCode($orderCode);

            if (!$payment) {
                return self::RESULT_NOT_FOUND;
            }

            if ($payment->trang_thai === LichSuPayos::STATUS_SUCCESS) {
                return self::RESULT_ALREADY_PROCESSED;
            }

            if ($code !== '00') {
                return self::RESULT_IGNORED;
            }

            $this->confirmPaid($payment, 'Nạp tiền PayOS', array_intersect_key($data, array_flip(self::BANK_FIELDS)));

            return self::RESULT_PROCESSED;
        });
    }

    /**
     * Ghi nhận một lệnh nạp đã được thanh toán. Gọi bên trong transaction,
     * với $payment đã lockForUpdate.
     */
    public function confirmPaid(LichSuPayos $payment, string $note, array $extra = []): void
    {
        $trungGian = User::lockForUpdate()->find((int) config('payment.trung_gian_id'));
        $nguoiNap  = User::lockForUpdate()->find($payment->admin_id);

        if (!$trungGian || !$nguoiNap) {
            throw new PaymentException('Không tìm thấy user');
        }

        $soTien = $payment->so_tien;

        $trungGian->decrement('vi_tien', $soTien);
        $nguoiNap->increment('vi_tien_mua_hang', $soTien);

        $trungGian->refresh();
        $nguoiNap->refresh();

        // Người trung gian
        LichSuGiaoDich::create([
            'sender_id'            => $trungGian->id,
            'receiver_id'          => $nguoiNap->id,
            'amount'               => $soTien,
            'note'                 => $note,
            'admin_id'             => $trungGian->id,
            'surplus'              => $trungGian->vi_tien,
            'surplus_vi_mua'       => $trungGian->vi_tien_mua_hang,
            'surplus_vi_mua_befor' => $trungGian->vi_tien_mua_hang,
            'type'                 => '1',
            'company_id'           => $trungGian->company_id,
        ]);

        // Người nạp tiền
        LichSuGiaoDich::create([
            'sender_id'            => $trungGian->id,
            'receiver_id'          => $nguoiNap->id,
            'amount'               => $soTien,
            'note'                 => $note . ' vào ví mua hàng',
            'admin_id'             => $nguoiNap->id,
            'surplus'              => $nguoiNap->vi_tien,
            'surplus_vi_mua'       => $nguoiNap->vi_tien_mua_hang,
            'surplus_vi_mua_befor' => $nguoiNap->vi_tien_mua_hang - $soTien,
            'type'                 => '0',
            'company_id'           => $nguoiNap->company_id,
        ]);

        $this->processOrder($payment);

        $payment->update(array_merge($extra, [
            'trang_thai' => LichSuPayos::STATUS_SUCCESS,
            'cancel'     => 0,
        ]));
    }

    /**
     * Tạo đơn hàng từ thông tin khách đã chọn lúc tạo link thanh toán.
     */
    public function processOrder(LichSuPayos $payment): void
    {
        if (empty($payment->product_json)) {
            return;
        }

        $data = json_decode($payment->product_json, true);

        if (!$data) {
            throw new PaymentException('Dữ liệu product_json không hợp lệ');
        }

        $user = User::find($payment->admin_id);
        if (!$user) {
            throw new PaymentException('Không tìm thấy người mua');
        }

        $product = Product::find($data['product_id'] ?? null);
        if (!$product) {
            throw new PaymentException('Không tìm thấy sản phẩm');
        }

        // OrderService lấy người mua từ auth()->user()
        Auth::setUser($user);

        $request = new Request();
        $request->replace($data);

        $fromApp = !empty($data[CheckoutService::FROM_APP_KEY]);

        $order = !empty($data['child_ids'])
            ? $this->orderService->muaTuChon($request, $product, $fromApp)
            : $this->orderService->muaGoi($request, $product, $fromApp);

        if (!empty($data['child_ids'])) {
            $this->cartService->removePurchased($user, $product->id, (array) $data['child_ids']);
        }

        $payment->update(['order_id' => $order->id]);
    }
}
