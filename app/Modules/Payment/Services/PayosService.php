<?php

namespace App\Modules\Payment\Services;

use App\Modules\Payment\Exceptions\PaymentException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Gọi API PayOS. Chỗ duy nhất biết URL, key và cách ký dữ liệu.
 */
class PayosService
{
    public const STATUS_PAID      = 'PAID';
    public const STATUS_CANCELLED = 'CANCELLED';
    public const STATUS_PENDING   = 'PENDING';
    public const STATUS_ERROR     = 'ERROR';

    public function generateOrderCode(): int
    {
        return intval(substr(strval(time()), -6) . str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT));
    }

    /**
     * Tạo link thanh toán, trả về `data` của PayOS (checkoutUrl, description, ...).
     */
    public function createPaymentLink(int $orderCode, int $amount, string $description, string $buyerName = ''): array
    {
        $returnBase = rtrim(config('app.url'), '/') . '/payos';

        $paymentData = [
            'orderCode'   => $orderCode,
            'amount'      => $amount,
            'description' => $description,
            'buyerName'   => $buyerName,
            'returnUrl'   => $returnBase . '/succes',
            'cancelUrl'   => $returnBase . '/cancel',
            'expiredAt'   => time() + (config('payment.payos.expire_minutes', 10) * 60),
        ];

        foreach (['orderCode', 'amount', 'description', 'returnUrl', 'cancelUrl'] as $key) {
            if (empty($paymentData[$key])) {
                throw new PaymentException("Thiếu tham số bắt buộc: {$key}", 400);
            }
        }

        $paymentData['signature'] = $this->signPaymentRequest($paymentData);

        try {
            $json = $this->http()->timeout(15)->post(config('payment.payos.api_url'), $paymentData)->json();
        } catch (\Throwable $e) {
            throw new PaymentException("Không kết nối được PayOS: {$e->getMessage()}", 500);
        }

        if (!isset($json['code'], $json['data'], $json['signature'])) {
            throw new PaymentException('Response không hợp lệ từ PayOS.', 500);
        }

        if ($json['code'] !== '00') {
            throw new PaymentException($json['desc'] ?? 'Unknown error from PayOS', (int) $json['code']);
        }

        if ($this->signData($json['data']) !== $json['signature']) {
            throw new PaymentException('Dữ liệu không toàn vẹn (sai chữ ký).', 500);
        }

        return $json['data'];
    }

    /**
     * Trạng thái thật của đơn trên PayOS: PAID | CANCELLED | PENDING | ERROR.
     */
    public function getPaymentStatus(int|string $orderCode): string
    {
        try {
            $data = $this->http()->timeout(10)
                ->get(config('payment.payos.api_url') . '/' . $orderCode)
                ->json();
        } catch (\Throwable $e) {
            Log::error('[PayOS] getPaymentStatus exception ma_don=' . $orderCode . ': ' . $e->getMessage());

            return self::STATUS_ERROR;
        }

        if (($data['code'] ?? '') !== '00') {
            Log::warning('[PayOS] getPaymentStatus: code không hợp lệ ma_don=' . $orderCode, (array) $data);

            return self::STATUS_ERROR;
        }

        return $data['data']['status'] ?? self::STATUS_PENDING;
    }

    private function http()
    {
        return Http::acceptJson()->withHeaders([
            'x-client-id' => config('payment.payos.client_id'),
            'x-api-key'   => config('payment.payos.api_key'),
        ]);
    }

    private function signPaymentRequest(array $data): string
    {
        $dataStr = "amount={$data['amount']}&cancelUrl={$data['cancelUrl']}&description={$data['description']}"
            . "&orderCode={$data['orderCode']}&returnUrl={$data['returnUrl']}";

        return hash_hmac('sha256', $dataStr, (string) config('payment.payos.checksum_key'));
    }

    private function signData(array $data): string
    {
        ksort($data);

        $parts = [];
        foreach ($data as $key => $value) {
            if ($value === null || $value === 'undefined' || $value === 'null') {
                $value = '';
            }
            $parts[] = "{$key}={$value}";
        }

        return hash_hmac('sha256', implode('&', $parts), (string) config('payment.payos.checksum_key'));
    }
}
