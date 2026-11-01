<?php

namespace App\Modules\Order\Services;

use App\Models\Order;
use App\Models\PancakeCommunes;
use App\Models\PancakeDistricts;
use App\Models\PancakeProvinces;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;

/**
 * Định dạng dữ liệu đơn hàng trả về API.
 */
class OrderPayloadService
{
    public function buyerPayload($user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'tel' => $user->tel,
            'wallet' => [
                'vi_tien' => (float) ($user->vi_tien ?? 0),
                'vi_tien_mua_hang' => (float) ($user->vi_tien_mua_hang ?? 0),
            ],
            'default_receiver' => [
                'name' => $user->name,
                'phone' => $user->tel,
                'address' => $user->address ?? null,
                'province_id' => $user->province_id ?? null,
                'district_id' => $user->district_id ?? null,
                'ward_id' => $user->ward_id ?? null,
            ],
        ];
    }

    public function productPayload(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => (float) ($product->price ?? 0),
            'quantity' => $product->quantity,
            'in_stock' => $product->quantity === null || (int) $product->quantity > 0,
            'old_price' => (float) ($product->old_price ?? 0),
            'price_hoan_tien' => (float) ($product->price_hoan_tien ?? 0),
            'purchase_price' => (float) ($product->purchase_price ?? 0),
            'image' => asset_url($product->image),
            'image_extra' => collect($product->image_extra ?? [])->map(fn ($path) => asset_url($path))->values(),
            'description' => $product->description,
            'content' => $product->content,
            'instructions' => $product->instructions,
            'licenses' => $product->licenses,
            'origin' => $product->origin,
            'parent_id' => $product->parent_id,
            'co_che_hoan_phi_id' => $product->co_che_hoan_phi_id,
            'san_pham_hoan' => (int) ($product->san_pham_hoan ?? 0),
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
            ] : null,
            'co_che_hoan_phi' => $product->coCheHoanPhi ? [
                'id' => $product->coCheHoanPhi->id,
                'name' => $product->coCheHoanPhi->name,
                'parent_id' => $product->coCheHoanPhi->parent_id,
                'trang_hoat_dong' => $product->coCheHoanPhi->trang_hoat_dong,
            ] : null,
        ];
    }

    private function receiverPreviewPayload(array $data): array
    {
        $province = !empty($data['province_id'])
            ? PancakeProvinces::where('id_pancake', $data['province_id'])->first()
            : null;
        $district = !empty($data['district_id'])
            ? PancakeDistricts::where('id_pancake', $data['district_id'])->first()
            : null;
        $ward = !empty($data['ward_id'])
            ? PancakeCommunes::where('id_pancake', $data['ward_id'])->first()
            : null;

        return [
            'name' => $data['receive_user'] ?? null,
            'phone' => $data['receive_phone'] ?? null,
            'address' => $data['receive_address'] ?? null,
            'province_id' => $data['province_id'] ?? null,
            'district_id' => $data['district_id'] ?? null,
            'ward_id' => $data['ward_id'] ?? null,
            'province' => $province?->name,
            'district' => $district?->name,
            'ward' => $ward?->name,
            'note' => $data['note'] ?? null,
        ];
    }

    public function orderPayload(Order $order, bool $includeAddress = false): array
    {
        $payload = [
            'id' => $order->id,
            'ma_don_hang' => $order->ma_don_hang,
            'name' => $order->name,
            'status' => $order->status,
            'total_price' => (float) ($order->total_price ?? 0),
            'total_price_hoan_tien' => (float) ($order->total_price_hoan_tien ?? 0),
            'price_vat' => (float) ($order->price_vat ?? 0),
            'shipping_price' => (float) ($order->shipping_price ?? 0),
            'total_quantity' => (int) $order->details->sum('quantity'),
            'first_order' => (int) ($order->first_order ?? 0),
            'device' => $order->device,
            'thoi_gian_mua' => $this->dateTimeString($order->thoi_gian_mua),
            'created_at' => $this->dateTimeString($order->created_at),
            'updated_at' => $this->dateTimeString($order->updated_at),
            'user' => $order->user ? [
                'id' => $order->user->id,
                'name' => $order->user->name,
                'tel' => $order->user->tel,
                'email' => $order->user->email,
            ] : null,
            'items' => $order->details->map(fn ($detail) => [
                'id' => $detail->id,
                'product_id' => $detail->product_id,
                'name' => $detail->name_product,
                'image' => asset_url($detail->image_product ?: $detail->product?->image),
                'price' => (float) ($detail->price_product ?? 0),
                'quantity' => (int) ($detail->quantity ?? 0),
                'line_total' => (float) ($detail->price_product ?? 0) * (int) ($detail->quantity ?? 0),
                'type' => $detail->the_loai ?? null,
                'product' => $detail->product ? $this->productPayload($detail->product) : null,
            ])->values(),
            'summary' => [
                'total_product_price' => (float) ($order->total_price ?? 0),
                'shipping_price' => (float) ($order->shipping_price ?? 0),
                'price_vat' => (float) ($order->price_vat ?? 0),
                'payable_total' => (float) ($order->total_price ?? 0),
                'total_price_hoan_tien' => (float) ($order->total_price_hoan_tien ?? 0),
            ],
        ];

        if ($includeAddress) {
            $payload['receiver'] = [
                'name' => $order->receive_user,
                'phone' => $order->receive_phone,
                'address' => $order->receive_address,
                'province_id' => $order->province_id,
                'district_id' => $order->district_id,
                'ward_id' => $order->ward_id,
                'province' => $order->province?->name,
                'district' => $order->district?->name,
                'ward' => $order->ward?->name,
                'note' => $order->note,
            ];
        }

        return $payload;
    }

    public function customPreviewPayload(Request $request, array $data): array
    {
        $parentProduct = Product::where('active', 1)
            ->availableForPurchase()
            ->with(['category', 'coCheHoanPhi'])
            ->findOrFail($data['product_id']);
        $products = Product::where('active', 1)
            ->availableForPurchase()
            ->whereIn('id', $data['child_ids'])
            ->with(['category', 'coCheHoanPhi'])
            ->get()
            ->keyBy('id');

        $items = collect();
        foreach ($data['child_ids'] as $index => $id) {
            $qty = (int) ($data['child_qtys'][$index] ?? 0);
            $product = $products->get((int) $id);

            if ($qty <= 0 || !$product) {
                continue;
            }

            $items->push([
                'product' => $this->productPayload($product),
                'quantity' => $qty,
                'unit_price' => (float) ($product->price ?? 0),
                'line_total' => (float) ($product->price ?? 0) * $qty,
            ]);
        }

        if ($items->isEmpty()) {
            abort(response()->json([
                'success' => false,
                'message' => 'Vui long chon it nhat 1 san pham voi so luong lon hon 0.',
                'data' => null,
                'meta' => (object) [],
                'links' => (object) [],
            ], 422));
        }

        $shippingFee = OrderService::SHIPPING_FEE_TU_CHON;
        $total = (float) $items->sum('line_total');
        $payableTotal = $total + $shippingFee;
        $totalHoan = max(0, $total - $shippingFee);
        $wallet = (float) ($request->user()->vi_tien_mua_hang ?? 0);

        return [
            'product' => $this->productPayload($parentProduct),
            'mode' => $data['mode'] ?? 'custom',
            'items' => $items->values(),
            'receiver' => $this->receiverPreviewPayload($data),
            'summary' => [
                'total_product_price' => $total,
                'shipping_price' => $shippingFee,
                'payable_total' => $payableTotal,
                'total_price_hoan_tien' => $totalHoan,
                'wallet' => $wallet,
                'can_pay_by_wallet' => $wallet >= $payableTotal,
                'missing_amount' => max(0, $payableTotal - $wallet),
            ],
        ];
    }

    private function dateTimeString($date): ?string
    {
        return $date ? $date->format('Y-m-d H:i:s') : null;
    }
}
