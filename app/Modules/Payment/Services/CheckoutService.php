<?php

namespace App\Modules\Payment\Services;

use App\Models\Product;
use App\Models\User;
use App\Modules\Payment\Exceptions\PaymentException;
use App\Modules\Payment\Models\LichSuPayos;
use App\Modules\Payment\Repositories\PaymentRepository;
use App\Services\OrderService;
use Illuminate\Support\Arr;

/**
 * Tính tiền đơn và tạo lệnh nạp tiền PayOS kèm thông tin đơn hàng
 * (webhook sẽ dùng product_json để tạo đơn sau khi khách trả tiền).
 */
class CheckoutService
{
    public const MIN_TOTAL         = 1500000;
    public const MIN_TOTAL_TU_CHON = 1674000;
    public const MAX_TOTAL         = 3000000;

    /** Đánh dấu trong product_json: lệnh nạp tạo từ app → đơn tạo ở webhook là đơn app */
    public const FROM_APP_KEY      = 'from_app';

    public function __construct(
        private readonly PayosService $payos,
        private readonly PaymentRepository $payments
    ) {
    }

    /**
     * Web: khách thanh toán toàn bộ đơn qua PayOS.
     */
    public function createPurchasePayment(array $data, User $user): LichSuPayos
    {
        $product  = Product::with('coCheHoanPhi.parent')->findOrFail($data['product_id']);
        $isCustom = !empty($data['child_ids']);
        $total    = $this->calculateTotal($data, $product);

        $this->ensureTotalInRange($total, $product, $isCustom);

        // Đơn web: không lưu device / thoi_gian_mua
        $payload = Arr::except($data, ['_token', 'total_price_hidden', 'total_hoan_hidden', 'device', self::FROM_APP_KEY]);
        $payload['backend_total_price'] = $total;

        return $this->createTopup($user, $total, $this->transferDescription($data['receive_user'] ?? ''), $payload);
    }

    /**
     * API (app): chỉ nạp phần ví mua hàng còn thiếu. Trả null nếu ví đã đủ.
     */
    public function createMissingAmountTopup(array $data, User $user): ?LichSuPayos
    {
        $product = Product::where('active', 1)->availableForPurchase()->findOrFail($data['product_id']);
        $amount  = $this->missingAmount($this->calculateTotal($data, $product), $user);

        if ($amount <= 0) {
            return null;
        }

        $payload = Arr::except($data, ['_token', 'confirmed']);
        $payload['backend_total_price'] = $amount;
        $payload[self::FROM_APP_KEY] = true;

        return $this->createTopup($user, $amount, 'nap tien', $payload);
    }

    /**
     * Tổng tiền phải trả cho đơn (mua tự chọn đã gồm phí ship).
     */
    public function calculateTotal(array $data, Product $product): int
    {
        if (!empty($data['child_ids'])) {
            return $this->sumChildren((array) $data['child_ids'], (array) ($data['child_qtys'] ?? []))
                + OrderService::SHIPPING_FEE_TU_CHON;
        }

        $qty = max(1, (int) ($data['qty'] ?? 1));

        return (int) $product->price * $qty;
    }

    public function missingAmount(int $total, User $user): int
    {
        return (int) max(0, ceil($total - (float) ($user->vi_tien_mua_hang ?? 0)));
    }

    public function createTopup(User $user, int $amount, string $description, array $payload): LichSuPayos
    {
        $orderCode = $this->payos->generateOrderCode();
        $response  = $this->payos->createPaymentLink($orderCode, $amount, $description, $user->name ?? '');

        return $this->payments->create([
            'admin_id'             => $user->id,
            'ma_don'               => $orderCode,
            'loai_don'             => 'Nạp tiền',
            'hinh_thuc_thanh_toan' => 'QR Pay',
            'so_tien'              => $amount,
            'trang_thai'           => LichSuPayos::STATUS_PENDING,
            'cancel'               => 0,
            'link'                 => $response['checkoutUrl'] ?? null,
            'description'          => $response['description'] ?? 'Nap tien',
            'product_json'         => json_encode($payload),
        ]);
    }

    private function sumChildren(array $childIds, array $childQtys): int
    {
        // Lọc giống OrderService::muaTuChon để số tiền nạp khớp số tiền bị trừ khi tạo đơn
        $products = Product::where('active', 1)
            ->availableForPurchase()
            ->whereIn('id', $childIds)
            ->get()
            ->keyBy('id');

        $total = 0;
        foreach ($childIds as $index => $id) {
            $qty   = max(0, (int) ($childQtys[$index] ?? 0));
            $child = $products->get((int) $id);

            if ($qty > 0 && $child) {
                $total += (int) $child->price * $qty;
            }
        }

        return $total;
    }

    private function ensureTotalInRange(int $total, Product $product, bool $isCustom): void
    {
        $min = $isCustom ? self::MIN_TOTAL_TU_CHON : self::MIN_TOTAL;

        if (!$this->isBinhAnZeroPercentProduct($product) && $total < $min) {
            throw new PaymentException('Vui lòng chọn đúng giá từ ' . number_format($min, 0, ',', '.') . 'đ');
        }

        if ($total > self::MAX_TOTAL) {
            throw new PaymentException('Vui lòng chọn đúng giá dưới ' . number_format(self::MAX_TOTAL, 0, ',', '.') . 'đ');
        }
    }

    /**
     * Nội dung chuyển khoản: tên cuối của người nhận + "tt".
     */
    private function transferDescription(string $receiveUser): string
    {
        $words = explode(' ', trim(preg_replace('/\s+/', ' ', $receiveUser)));

        return mb_strtolower(end($words)) . ' tt';
    }

    private function isBinhAnZeroPercentProduct(Product $product): bool
    {
        $coChe = $product->coCheHoanPhi;
        if (!$coChe) {
            return false;
        }

        $source = trim(implode(' ', array_filter([$coChe->name, $coChe->parent?->name])));
        $normalized = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $source);
        $source = strtolower($normalized !== false ? $normalized : $source);

        return str_contains($source, 'binh an') && (float) ($coChe->tang_1 ?? 0) == 0.0;
    }
}
