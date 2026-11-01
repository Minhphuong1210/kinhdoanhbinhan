<?php

namespace App\Modules\Order\Services;

use App\Models\PancakeCommunes;
use App\Models\PancakeDistricts;
use App\Models\PancakeProvinces;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Support\Collection;

/**
 * Các bước trước khi đặt hàng: chọn sản phẩm, xác nhận đơn tự chọn.
 */
class ShopService
{
    public function provinces(): Collection
    {
        return PancakeProvinces::whereNotNull('new_id')->orderBy('name')->get(['id_pancake as id', 'name']);
    }

    /** Dữ liệu trang xác nhận đơn tự chọn; items rỗng nghĩa là chưa chọn sản phẩm */
    public function confirmData(array $data, User $user): array
    {
        $products = Product::with('category')
            ->availableForPurchase()
            ->whereIn('id', $data['child_ids'])
            ->get()
            ->keyBy('id');

        $items = collect();

        foreach ($data['child_ids'] as $index => $id) {
            $qty = (int) ($data['child_qtys'][$index] ?? 0);
            $child = $products->get((int) $id);

            if ($qty > 0 && $child) {
                $items->push([
                    'product'    => $child,
                    'qty'        => $qty,
                    'line_total' => $child->price * $qty,
                ]);
            }
        }

        $shippingFee = OrderService::SHIPPING_FEE_TU_CHON;
        $total = $items->sum('line_total');
        $payableTotal = $total + $shippingFee;
        $wallet = (int) ($user->vi_tien_mua_hang ?? 0);

        return [
            'items'         => $items,
            'total'         => $total,
            'shippingFee'   => $shippingFee,
            'payableTotal'  => $payableTotal,
            'totalHoan'     => max(0, $total - $shippingFee),
            'wallet'        => $wallet,
            'paymentAction' => $wallet >= $payableTotal ? route('admin.orders.store') : route('payos.create-payment-link'),
            'province'      => PancakeProvinces::where('id_pancake', $data['province_id'])->first(),
            'district'      => PancakeDistricts::where('id_pancake', $data['district_id'])->first(),
            'ward'          => PancakeCommunes::where('id_pancake', $data['ward_id'])->first(),
        ];
    }
}
