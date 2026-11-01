<?php

namespace App\Modules\Dashboard\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

/**
 * Truy vấn thống kê doanh thu cho admin.
 * Giá trị ghép vào SQL đều là hằng số hoặc đã ép kiểu số, không lấy từ request.
 */
class DashboardRepository
{
    public function orderDetailTotalsQuery()
    {
        return DB::table('order_details')
            ->select(
                'order_id',
                DB::raw($this->lineRevenueSumSql() . ' as order_detail_revenue')
            )
            ->where(function ($q) {
                $q->whereNull('the_loai')
                    ->orWhere('the_loai', 'LIKE', 'product%');
            })
            ->groupBy('order_id');
    }

    public function refundableOrderDetailsQuery($orderIds, ?string $type, $orderDetailTotals)
    {
        $query = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->leftJoin('products', 'order_details.product_id', '=', 'products.id')
            ->leftJoinSub($orderDetailTotals, 'order_detail_totals', function ($join) {
                $join->on('order_details.order_id', '=', 'order_detail_totals.order_id');
            })
            ->whereIn('orders.id', $orderIds)
            ->where('orders.status', 'completed')
            ->where(function ($q) {
                $q->whereNull('order_details.the_loai')
                    ->orWhere('order_details.the_loai', 'LIKE', 'product%');
            })
            ->where(function ($q) {
                $q->where('orders.ma_don_hang', 'LIKE', 'TC%')
                    ->orWhereNull('products.san_pham_hoan')
                    ->orWhere('products.san_pham_hoan', '!=', 1);
            });

        if ($type === 'combo') {
            $query->where('orders.ma_don_hang', 'NOT LIKE', 'TC%')
                ->where('orders.name', 'LIKE', 'COMBO%');
        }

        if ($type === 'tuchon') {
            $query->where('orders.ma_don_hang', 'LIKE', 'TC%');
        }

        if ($type === 'combo_or_tuchon') {
            $query->where(function ($q) {
                $q->where('orders.ma_don_hang', 'LIKE', 'TC%')
                    ->orWhere(function ($q) {
                        $q->where('orders.ma_don_hang', 'NOT LIKE', 'TC%')
                            ->where('orders.name', 'LIKE', 'COMBO%');
                    });
            });
        }

        return $query;
    }

    public function lineRevenueSql(): string
    {
        return 'COALESCE(order_details.quantity, 0) * COALESCE(order_details.price_product, 0)';
    }

    public function lineRevenueSumSql(): string
    {
        return 'SUM(' . $this->lineRevenueSql() . ')';
    }

    public function allocatedOrderSql(string $column): string
    {
        return 'CASE WHEN order_detail_totals.order_detail_revenue > 0 '
            . 'THEN COALESCE(' . $column . ', 0) * (' . $this->lineRevenueSql() . ') / order_detail_totals.order_detail_revenue '
            . 'ELSE 0 END';
    }

    public function allocatedOrderSumSql(string $column): string
    {
        return 'SUM(' . $this->allocatedOrderSql($column) . ')';
    }

    public function comboQuantitySql(): string
    {
        return "SUM(CASE WHEN orders.ma_don_hang LIKE 'TC%' THEN 0 ELSE COALESCE(order_details.quantity, 0) END)"
            . " + COUNT(DISTINCT CASE WHEN orders.ma_don_hang LIKE 'TC%' THEN orders.id END)";
    }

    public function costEligibleOrderSql(): string
    {
        return "(orders.ma_don_hang LIKE 'DH%' OR orders.ma_don_hang LIKE 'TC%')";
    }

    public function getPurchasePrice()
    {
        return [
            'combo_1'  => 370000,

            'combo_2'  => 345000,
            'combo_3'  => 345000,
            'combo_4'  => 376000,
            'combo_5'  => 345000,
            'combo_6'  => 345000,
            'combo_7'  => 345000,

            'combo_8'  => 350000,
            'combo_9'  => 350000,
            'combo_10' => 356000,
            'combo_11' => 350000,

            'combo_12' => 0,
            'combo_13' => 0,
            'combo_14' => 0,
            'combo_15' => 0,

            'combo_16' => 330000,

            'combo_21' => 250000,
            'combo_26' => 250000,
            'combo_29' => 225000,
        ];
    }

    public function getPurchasePriceByProductId(array $purchasePrice): array
    {
        return Product::query()
            ->where(function ($query) {
                $query->whereNotNull('purchase_price')
                    ->orWhere('slug', 'like', 'combo-%');
            })
            ->get(['id', 'slug', 'purchase_price'])
            ->mapWithKeys(function ($product) use ($purchasePrice) {
                preg_match('/^combo-(\d+)(-|$)/', $product->slug, $matches);

                $comboKey = isset($matches[1]) ? 'combo_' . $matches[1] : null;

                if ($product->purchase_price !== null) {
                    return [$product->id => $product->purchase_price];
                }

                if (!$comboKey || !array_key_exists($comboKey, $purchasePrice)) {
                    return [];
                }

                return [$product->id => $purchasePrice[$comboKey]];
            })
            ->all();
    }

    public function getPurchasePriceCase(array $purchasePriceProductId): string
    {
        $case = 'CASE';

        foreach ($purchasePriceProductId as $productId => $price) {
            $case .= sprintf(' WHEN products.id = %d THEN %d', (int)$productId, (int)$price);
        }

        return $case . ' ELSE 0 END';
    }

    public function customComboProductIdSql(): string
    {
        $comboProductIds = $this->getCustomComboProductIds();
        $case = 'CASE';
        uksort($comboProductIds['combo'], function ($a, $b) {
            return strlen($b) <=> strlen($a) ?: strnatcasecmp($b, $a);
        });

        foreach ($comboProductIds['combo'] as $comboNumber => $productId) {
            $comboNumberForSql = strtoupper($comboNumber);
            $case .= sprintf(
                " WHEN orders.ma_don_hang LIKE 'TCCOMBO%s%%' THEN %d",
                str_replace("'", "''", $comboNumberForSql),
                (int)$productId
            );
            $case .= sprintf(
                " WHEN orders.name REGEXP 'COMBO[[:space:]-]*%s([^0-9A-Z]|$)' THEN %d",
                str_replace("'", "''", $comboNumberForSql),
                (int)$productId
            );
        }

        if ($comboProductIds['tc_spltt_lien_ket']) {
            $case .= sprintf(
                " WHEN orders.ma_don_hang LIKE 'TCSPLTTLIENKET%%' THEN %d",
                (int)$comboProductIds['tc_spltt_lien_ket']
            );
        }

        if ($comboProductIds['tc_splk_hp40']) {
            $case .= sprintf(
                " WHEN orders.ma_don_hang LIKE 'TCSPLKHP40%%' THEN %d",
                (int)$comboProductIds['tc_splk_hp40']
            );
        }

        return $case . ' ELSE NULL END';
    }

    public function getCustomComboProductIds(): array
    {
        $map = [
            'combo' => [],
            'tc_spltt_lien_ket' => null,
            'tc_splk_hp40' => null,
        ];

        Product::query()
            ->with('coCheHoanPhi.parent')
            ->get(['id', 'name', 'slug', 'co_che_hoan_phi_id'])
            ->each(function ($product) use (&$map) {
                $source = trim(implode(' ', array_filter([
                    $product->slug ?? null,
                    $product->name ?? null,
                    $product->coCheHoanPhi?->name,
                    $product->coCheHoanPhi?->parent?->name,
                ])));

                if (preg_match('/combo[\s_-]*([0-9]+[a-z]?)/i', $source, $matches)) {
                    $map['combo'][strtoupper($matches[1])] = (int)$product->id;
                }

                if (preg_match('/tc[\s_-]*spltt[\s_-]*lien[\s_-]*ket/i', $source)) {
                    $map['tc_spltt_lien_ket'] = (int)$product->id;
                }

                $normalized = normalize_search_text($source);
                $rate = (float)($product->coCheHoanPhi?->tang_1 ?? 0);
                if ($rate == 40.0 || str_contains($normalized, '40') || str_contains($normalized, 'lien ket') || str_contains($normalized, 'splk')) {
                    $map['tc_splk_hp40'] = (int)$product->id;
                }
            });

        return $map;
    }
}
