<?php

namespace App\Modules\Dashboard\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Modules\Category\Models\Category;
use App\Modules\Dashboard\Repositories\DashboardRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function __construct(private readonly DashboardRepository $stats)
    {
    }

    public function memberData(User $user): array
    {
        $stats = Cache::remember('user_dashboard_stats_' . $user->id, 300, function () use ($user) {
            $myOrders = Order::where('user_id', $user->id);

            return [
                'my_orders'    => (clone $myOrders)->count(),
                'my_pending'   => (clone $myOrders)->where('status', 'pending')->count(),
                'my_completed' => (clone $myOrders)->where('status', 'completed')->count(),
                'my_cancelled' => (clone $myOrders)->where('status', 'cancelled')->count(),
            ];
        });

        return ['stats' => $stats, 'topUsers' => [], 'categorySales' => [], 'monthlyStats' => null];
    }

    public function adminData(?string $fromDate, ?string $toDate): array
    {

        $orderQuery = Order::query();

        if ($fromDate) {
            $orderQuery->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $orderQuery->whereDate('created_at', '<=', $toDate);
        }

        $orderIds = $orderQuery->pluck('id');

        $purchasePrice          = $this->stats->getPurchasePrice();
        $purchasePriceProductId = $this->stats->getPurchasePriceByProductId($purchasePrice);
        $purchasePriceCase      = $this->stats->getPurchasePriceCase($purchasePriceProductId);
        $orderDetailTotals      = $this->stats->orderDetailTotalsQuery();

        // =====  =====
        $monthlyStats = $this->stats->refundableOrderDetailsQuery($orderIds, 'combo_or_tuchon', $orderDetailTotals)
            ->select(
                DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m") as month'),
                DB::raw($this->stats->comboQuantitySql() . ' as total_combo'),
                DB::raw($this->stats->lineRevenueSumSql() . ' as total_buy'),
                DB::raw($this->stats->allocatedOrderSumSql('orders.price_vat') . ' as total_vat'),
                DB::raw($this->stats->allocatedOrderSumSql('orders.shipping_price') . ' as total_shipping')
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();



        $monthlyRefund = DB::table('lich_su_giao_dich')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total_refund')
            )
            ->where('type', 2)
            ->where('note', 'like', '%NH%M TI%U D%NG%')
            ->when($fromDate, fn($q) => $q->whereDate('created_at', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('created_at', '<=', $toDate))
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $monthlyCosts = $this->stats->refundableOrderDetailsQuery($orderIds, 'combo_or_tuchon', $orderDetailTotals)
            ->select(
                DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m") as month'),
                DB::raw("SUM(CASE WHEN {$this->stats->costEligibleOrderSql()} THEN order_details.quantity * ({$purchasePriceCase}) ELSE 0 END) as total_cost")
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $monthlyQuantities = $this->stats->refundableOrderDetailsQuery($orderIds, 'combo_or_tuchon', $orderDetailTotals)
            ->select(
                DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m") as month'),
                DB::raw($this->stats->comboQuantitySql() . ' as total_qty_order')
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        foreach ($monthlyStats as $item) {
            $item->total_combo  = $monthlyQuantities[$item->month]->total_qty_order ?? 0;
            $item->total_refund = $monthlyRefund[$item->month]->total_refund ?? 0;
            $item->total_cost   = $monthlyCosts[$item->month]->total_cost ?? 0;
            $item->profit       = $item->total_buy - (
                    $item->total_vat
                    + $item->total_shipping
                    + $item->total_cost
                    + $item->total_refund
                );
        }

//  foreach ($monthlyStats as $item) {
//                 $item->total_refund = $monthlyRefund[$item->month]->total_refund ?? 0;
//             }



        $stats = [
            'products'                => Product::count(),
            'orders'                  => Order::whereIn('id', $orderIds)->count(),
            'users'                   => User::count(),
            'categories'              => Category::count(),

            'revenue'                 => Order::whereIn('id', $orderIds)
                ->where('status', 'completed')
                ->sum('total_price'),

            'pending'                 => Order::whereIn('id', $orderIds)
                ->where('status', 'pending')
                ->count(),

            'total_qty'               => $this->stats->refundableOrderDetailsQuery($orderIds, 'combo', $orderDetailTotals)
                ->sum('order_details.quantity'),

            'total_buy'               => $this->stats->refundableOrderDetailsQuery($orderIds, 'combo', $orderDetailTotals)
                ->sum(DB::raw($this->stats->lineRevenueSql())),

            'total_buy_price_vat'     => $this->stats->refundableOrderDetailsQuery($orderIds, 'combo', $orderDetailTotals)
                ->sum(DB::raw($this->stats->allocatedOrderSql('orders.price_vat'))),

            'total_buy_fee_shipping'  => $this->stats->refundableOrderDetailsQuery($orderIds, 'combo', $orderDetailTotals)
                ->sum(DB::raw($this->stats->allocatedOrderSql('orders.shipping_price'))),

            'total_buy_not_VAT'       => $this->stats->refundableOrderDetailsQuery($orderIds, 'combo', $orderDetailTotals)
                ->sum(DB::raw($this->stats->allocatedOrderSql('orders.total_price_hoan_tien'))),

            'total_refund'            => DB::table('lich_su_giao_dich')
                ->where('type', 2)
                ->where('note', 'like', '%NH%M TI%U D%NG%')
                ->where('note', 'not like', '%tự chọn%')
                ->sum('amount'),

            'tuchon_qty'              => $this->stats->refundableOrderDetailsQuery($orderIds, 'tuchon', $orderDetailTotals)
                ->count(DB::raw('DISTINCT orders.id')),

            'tuchon_buy'              => $this->stats->refundableOrderDetailsQuery($orderIds, 'tuchon', $orderDetailTotals)
                ->sum(DB::raw($this->stats->lineRevenueSql())),

            'tuchon_buy_price_vat'    => $this->stats->refundableOrderDetailsQuery($orderIds, 'tuchon', $orderDetailTotals)
                ->sum(DB::raw($this->stats->allocatedOrderSql('orders.price_vat'))),

            'tuchon_buy_fee_shipping' => $this->stats->refundableOrderDetailsQuery($orderIds, 'tuchon', $orderDetailTotals)
                ->sum(DB::raw($this->stats->allocatedOrderSql('orders.shipping_price'))),

            'tuchon_buy_not_VAT'      => $this->stats->refundableOrderDetailsQuery($orderIds, 'tuchon', $orderDetailTotals)
                ->sum(DB::raw($this->stats->allocatedOrderSql('orders.total_price_hoan_tien') . ' - ' . $this->stats->allocatedOrderSql('orders.price_vat'))),

            'tuchon_refund'           => DB::table('lich_su_giao_dich')
                ->where('type', 2)
                ->where('note', 'like', '%NH%M TI%U D%NG%')
                ->where('note', 'like', '%tự chọn%')
                ->sum('amount'),
        ];

        $stats['so_tien_rut_tru_thue'] = DB::table('yeu_cau_rut_tien')->where('status', 1)->sum('so_tien_sau_thue');

        $stats['so_tien_da_rut'] = DB::table('yeu_cau_rut_tien')->where('status', 1)->sum('so_tien');

        $stats['10_phan_tram_so_tien'] = DB::table('yeu_cau_rut_tien')->where('status', 1)->sum('10_phan_tram_so_tien');

        $stats['1_5_phan_tram_so_tien'] = DB::table('yeu_cau_rut_tien')->where('status', 1)->sum('1_5_phan_tram_so_tien');

        $stats['so_tien_da_tra_thuc_te'] = $stats['so_tien_da_rut'] - $stats['so_tien_rut_tru_thue'];

        $stats['so_tien_thanh_vien_chua_du_3_f1'] = User::where('so_thanh_vien_gt', '<', 3)
            ->where('id', '!=', 22205)
            ->sum('vi_tien');

        $stats['tong_tien_thuong_hieu_binh_an'] = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->whereIn('orders.id', $orderIds)
            ->where('orders.status', 'completed')
            ->where('products.category_id', 3)
            ->sum(DB::raw('order_details.quantity * order_details.price_product'));

        $stats['thanh_vien_du_3f1_tien_duoi_500K'] =
            User::where('so_thanh_vien_gt', 3)
                ->where('vi_tien', '<', 500000)
                ->sum('vi_tien');

        $topUsers = User::where('is_admin', 0)
            ->orderByDesc('vi_tien')
            ->limit(100)
            ->get(['name', 'tel', 'email', 'vi_tien', 'address']);

        $customComboProductIdSql = $this->stats->customComboProductIdSql();
        $categoryNameSql = "CASE "
            . "WHEN orders.ma_don_hang LIKE 'TC%' THEN 'Combo tự chọn' "
            . "ELSE categories.name "
            . "END";
        $productIdSql = "CASE "
            . "WHEN orders.ma_don_hang LIKE 'TCSPLKHP40%' THEN NULL "
            . "WHEN orders.ma_don_hang LIKE 'TCSPLTTLIENKET%' THEN NULL "
            . "WHEN orders.ma_don_hang LIKE 'TC%' THEN {$customComboProductIdSql} "
            . "ELSE order_details.product_id "
            . "END";
        $tcComboCases = [];
        foreach (array_merge(['2A'], range(29, 1)) as $comboNumber) {
            $tcComboCases[] = "WHEN orders.name REGEXP 'COMBO[[:space:]-]*{$comboNumber}([^0-9A-Z]|$)' THEN 'TCCOMBO{$comboNumber}'";
        }
        $tcComboCases[] = "WHEN orders.name REGEXP '(BINH[[:space:]-]*AN.*50|50.*BINH[[:space:]-]*AN)' THEN 'TCCOMBO20'";
        $tcComboCases[] = "WHEN orders.name REGEXP 'LIEN[[:space:]-]*KET|SPLK|HOAN[[:space:]-]*PHI.*40|40.*HOAN[[:space:]-]*PHI' THEN 'TCSPLKHP40'";
        $legacyTcComboSql = 'CASE ' . implode(' ', $tcComboCases) . " ELSE 'TCSPLKHP40' END";
        $productNameSql = "CASE "
            . "WHEN orders.ma_don_hang LIKE 'TCSPLKHP40%' THEN 'TCSPLKHP40' "
            . "WHEN orders.ma_don_hang LIKE 'TCSPLTTLIENKET%' THEN 'TCSPLKHP40' "
            . "WHEN orders.ma_don_hang LIKE 'TCCOMBO%' THEN CONCAT('TCCOMBO', SUBSTRING(orders.ma_don_hang, 8, CHAR_LENGTH(orders.ma_don_hang) - 13)) "
            . "WHEN orders.ma_don_hang LIKE 'TC%' THEN {$legacyTcComboSql} "
            . "ELSE COALESCE(products.name, CONCAT('Sản phẩm đã xóa #', order_details.product_id)) "
            . "END";

        $categorySales = $this->stats->refundableOrderDetailsQuery($orderIds, null, $orderDetailTotals)
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')

            ->select(
                DB::raw($categoryNameSql . ' as category_name'),
                DB::raw($productNameSql . ' as product_name'),
                DB::raw($productIdSql . ' as product_id'),
                DB::raw($this->stats->comboQuantitySql() . ' as total_qty_order'),
                DB::raw($this->stats->lineRevenueSumSql() . ' as total_revenue'),
                DB::raw("SUM(CASE WHEN {$this->stats->costEligibleOrderSql()} THEN order_details.quantity * ({$purchasePriceCase}) ELSE 0 END) as total_cost"),
                DB::raw($this->stats->allocatedOrderSumSql('orders.price_vat') . ' as total_vat'),
                DB::raw($this->stats->allocatedOrderSumSql('orders.shipping_price') . ' as total_shipping'),
                DB::raw('0 as total_refund')
            )

            ->groupBy(
                DB::raw($categoryNameSql),
                DB::raw($productNameSql),
                DB::raw($productIdSql)
            )
            ->orderByDesc('total_revenue')
            ->get();

        $refundTransactions = DB::table('lich_su_giao_dich')
            ->select('id', 'note', 'amount', 'product_id')
            ->where('type', 2)
            ->where('note', 'like', '%NH%M TI%U D%NG%')
            ->when($fromDate, fn($q) => $q->whereDate('created_at', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->whereDate('created_at', '<=', $toDate))
            ->get();

        $productNameByRefundKey = [];
        $refundByProductId = [];
        $categoryProductIds = [];

        foreach ($categorySales as $item) {
            $refundKey = $this->getRefundProductKey($item->product_name);

            if ($refundKey) {
                $productNameByRefundKey[$refundKey] = $item->product_name;
            }

            if ($item->product_id) {
                $categoryProductIds[(int)$item->product_id] = true;
            }
        }

        $legacyRefundByProductName = [];
        $unmatchedRefund     = 0;
        $unmatchedRefundIds  = [];
        $refundProductKeysByProductId = Product::query()
            ->with('coCheHoanPhi.parent')
            ->whereIn('id', $refundTransactions->pluck('product_id')->filter()->unique()->values())
            ->get(['id', 'name', 'slug', 'co_che_hoan_phi_id'])
            ->mapWithKeys(fn($product) => [(int)$product->id => $this->getRefundProductKeyFromProduct($product)])
            ->filter()
            ->all();

        foreach ($refundTransactions as $transaction) {
            if ($transaction->product_id) {
                $productId = (int)$transaction->product_id;
                if (isset($categoryProductIds[$productId])) {
                    $refundByProductId[$productId] = ($refundByProductId[$productId] ?? 0) + (float)$transaction->amount;
                } elseif (($matchedName = $productNameByRefundKey[$refundProductKeysByProductId[$productId] ?? null] ?? null)) {
                    $legacyRefundByProductName[$matchedName] = ($legacyRefundByProductName[$matchedName] ?? 0) + (float)$transaction->amount;
                } elseif ($matchedName = $this->refundProductNameFromNote($transaction->note, $productNameByRefundKey)) {
                    $legacyRefundByProductName[$matchedName] = ($legacyRefundByProductName[$matchedName] ?? 0) + (float)$transaction->amount;
                } else {
                    $unmatchedRefund += (float)$transaction->amount;
                    $unmatchedRefundIds[] = $transaction->id;
                }
                continue;
            }

            $refundKey   = $this->getRefundProductKey($transaction->note);
            $matchedName = $refundKey ? ($productNameByRefundKey[$refundKey] ?? null) : null;

            if ($matchedName === null) {
                $unmatchedRefund += (float)$transaction->amount;
                $unmatchedRefundIds[] = $transaction->id;
                continue;
            }

            $legacyRefundByProductName[$matchedName] = ($legacyRefundByProductName[$matchedName] ?? 0) + (float)$transaction->amount;
        }

        foreach ($categorySales as $item) {
            $matchedRefund = $item->product_id
                ? ($refundByProductId[(int)$item->product_id] ?? 0)
                : 0;

            $matchedRefund += $legacyRefundByProductName[$item->product_name] ?? 0;

            $item->total_refund = $matchedRefund;

            $item->profit = $item->total_revenue - (
                    $item->total_vat
                    + $item->total_shipping
                    + $item->total_cost
                    + $item->total_refund
                );
        }

        if ($unmatchedRefund > 0) {
            $categorySales->push((object)[
                'category_name'    => 'Hoàn phí',
                'product_name'     => 'Hoàn phí không xác định sản phẩm',
                'product_id'       => null,
                'total_qty_order'  => 0,
                'total_revenue'    => 0,
                'total_cost'       => 0,
                'total_vat'        => 0,
                'total_shipping'   => 0,
                'total_refund'     => $unmatchedRefund,
                'profit'           => -$unmatchedRefund,
                'refund_transaction_ids' => $unmatchedRefundIds,
            ]);
        }

        $stats['total_profit'] = $categorySales->sum('profit');


        return compact('stats', 'topUsers', 'categorySales', 'monthlyStats');
    }

    private function refundProductNameFromNote(?string $note, array $productNameByRefundKey): ?string
    {
        $refundKey = $this->getRefundProductKey($note);

        return $refundKey ? ($productNameByRefundKey[$refundKey] ?? null) : null;
    }

    private function getRefundProductKeyFromProduct(Product $product): ?string
    {
        $source = trim(implode(' ', array_filter([
            $product->slug ?? null,
            $product->name ?? null,
            $product->coCheHoanPhi?->name,
            $product->coCheHoanPhi?->parent?->name,
        ])));
        $normalized = normalize_search_text($source);
        $rate = (float)($product->coCheHoanPhi?->tang_1 ?? 0);

        if (preg_match('/combo[\s_-]*([0-9]+[a-z]?)/i', $source, $matches)) {
            return 'tc_combo_' . strtolower($matches[1]);
        }

        if (($rate == 50.0 || str_contains($normalized, '50')) && str_contains($normalized, 'binh an')) {
            return 'tc_combo_20';
        }

        if ($rate == 40.0 || str_contains($normalized, 'lien ket') || str_contains($normalized, 'splk')) {
            return 'tc_splk_hp40';
        }

        return null;
    }

    private function getRefundProductKey(?string $text): ?string
    {
        $normalized = normalize_search_text((string)$text);

        if (str_contains($normalized, 'tcsplkhp40')
            || str_contains($normalized, 'tcsplttlienket')
            || str_contains($normalized, 'splk')
            || str_contains($normalized, 'lien ket')
            || preg_match('/hoan\s*phi.*40|40.*hoan\s*phi/', $normalized)) {
            return 'tc_splk_hp40';
        }

        if (!$text || !preg_match('/(?:TC)?COMBO\s*([0-9]+[A-Z]?)/i', $text, $matches)) {
            return null;
        }

        $isCustom = preg_match('/\bTCCOMBO/i', $text)
            || preg_match('/mua\s+t.*ch.*n/i', $text);
        $prefix = $isCustom ? 'tc_combo_' : 'combo_';

        return $prefix . strtolower($matches[1]);
    }
}
