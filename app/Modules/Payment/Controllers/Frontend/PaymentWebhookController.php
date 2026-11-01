<?php

namespace App\Modules\Payment\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Modules\Payment\Exceptions\PaymentException;
use App\Modules\Payment\Services\PaymentWebhookService;
use App\Modules\Payment\Services\PayosService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __construct(
        private readonly PaymentWebhookService $webhookService,
        private readonly PayosService $payos
    ) {
    }

    /**
     * POST /payos-webhook
     */
    public function webhook(Request $request): JsonResponse
    {
        Log::info('[PayOS webhook]', $request->all());

        $data = (array) $request->input('data', []);

        if (!$this->payos->isValidWebhook($data, $request->input('signature'))) {
            Log::warning('[PayOS webhook] sai chữ ký', ['ip' => $request->ip()]);

            return $this->reply(-1, 'Sai chữ ký', 401);
        }

        $orderCode = $data['orderCode'] ?? null;

        if (!$orderCode) {
            return $this->reply(-1, 'Thiếu orderCode', 400);
        }

        try {
            $result = $this->webhookService->handle(
                $orderCode,
                $request->input('code'),
                $data
            );
        } catch (PaymentException $e) {
            Log::error('[PayOS webhook] lỗi: ' . $e->getMessage());

            return $this->reply(-1, $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('[PayOS webhook] lỗi: ' . $e->getMessage());

            return $this->reply(-1, 'Lỗi xử lý', 500);
        }

        return match ($result) {
            PaymentWebhookService::RESULT_NOT_FOUND         => $this->reply(0, 'Không tìm thấy đơn hàng', 404),
            PaymentWebhookService::RESULT_ALREADY_PROCESSED => $this->reply(0, 'Đã xử lý trước đó'),
            default                                         => $this->reply(0, 'Thành công'),
        };
    }

    private function reply(int $code, string $message, int $status = 200): JsonResponse
    {
        return response()->json(['code' => $code, 'message' => $message], $status);
    }
}
