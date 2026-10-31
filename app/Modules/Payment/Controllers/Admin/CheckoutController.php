<?php

namespace App\Modules\Payment\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Payment\Exceptions\PaymentException;
use App\Modules\Payment\Requests\CreatePaymentLinkRequest;
use App\Modules\Payment\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(private readonly CheckoutService $checkoutService)
    {
    }

    /**
     * POST /payos/create-payment-link
     * Tạo link thanh toán PayOS rồi redirect.
     */
    public function createPaymentLink(CreatePaymentLinkRequest $request): RedirectResponse
    {
        return $this->redirectToPayment($request);
    }

    /**
     * Dùng chung cho các chỗ đã validate sẵn đơn hàng (vd. OrderController khi ví không đủ).
     */
    public function redirectToPayment(Request $request): RedirectResponse
    {
        try {
            $payment = $this->checkoutService->createPurchasePayment($request->all(), $request->user());

            return redirect($payment->link);
        } catch (\Throwable $e) {
            $message = match (true) {
                $e->getCode() === 503 => 'Hệ thống PayOS đang gián đoạn. Xin lỗi vì sự bất tiện.',
                $e instanceof PaymentException &&$e->getCode() === 0 => $e->getMessage(),
                default => "Lỗi: {$e->getMessage()} | Code: {$e->getCode()}",
            };

            return redirect(CreatePaymentLinkRequest::buyUrl($request->all()))
                ->withInput()
                ->with('error', $message);
        }
    }
}
