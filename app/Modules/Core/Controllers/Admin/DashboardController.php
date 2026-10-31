<?php
namespace App\Modules\Core\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Category\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $user          = auth()->user();
        $isAdmin       = $user->is_admin;
        $monthlyStats  = null;
        $monthlyRefund = null;
        if ($isAdmin) {

            $fromDate = $request->from_date;
            $toDate   = $request->to_date;

            $orderQuery = Order::query();

            if ($fromDate) {
                $orderQuery->whereDate('created_at', '>=', $fromDate);
            }

            if ($toDate) {
                $orderQuery->whereDate('created_at', '<=', $toDate);
            }

            $orderIds = $orderQuery->pluck('id');

            $purchasePrice          = $this->getPurchasePrice();
            $purchasePriceProductId = $this->getPurchasePriceByProductId($purchasePrice);
            $purchasePriceCase      = $this->getPurchasePriceCase($purchasePriceProductId);
            $orderDetailTotals      = $this->orderDetailTotalsQuery();

            // =====  =====
            $monthlyStats = $this->refundableOrderDetailsQuery($orderIds, 'combo_or_tuchon', $orderDetailTotals)
                ->select(
                    DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m") as month'),
                    DB::raw($this->comboQuantitySql() . ' as total_combo'),
                    DB::raw($this->lineRevenueSumSql() . ' as total_buy'),
                    DB::raw($this->allocatedOrderSumSql('orders.price_vat') . ' as total_vat'),
                    DB::raw($this->allocatedOrderSumSql('orders.shipping_price') . ' as total_shipping')
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

            $monthlyCosts = $this->refundableOrderDetailsQuery($orderIds, 'combo_or_tuchon', $orderDetailTotals)
                ->select(
                    DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m") as month'),
                    DB::raw("SUM(CASE WHEN {$this->costEligibleOrderSql()} THEN order_details.quantity * ({$purchasePriceCase}) ELSE 0 END) as total_cost")
                )
                ->groupBy('month')
                ->get()
                ->keyBy('month');

            $monthlyQuantities = $this->refundableOrderDetailsQuery($orderIds, 'combo_or_tuchon', $orderDetailTotals)
                ->select(
                    DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m") as month'),
                    DB::raw($this->comboQuantitySql() . ' as total_qty_order')
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

                'total_qty'               => $this->refundableOrderDetailsQuery($orderIds, 'combo', $orderDetailTotals)
                    ->sum('order_details.quantity'),

                'total_buy'               => $this->refundableOrderDetailsQuery($orderIds, 'combo', $orderDetailTotals)
                    ->sum(DB::raw($this->lineRevenueSql())),

                'total_buy_price_vat'     => $this->refundableOrderDetailsQuery($orderIds, 'combo', $orderDetailTotals)
                    ->sum(DB::raw($this->allocatedOrderSql('orders.price_vat'))),

                'total_buy_fee_shipping'  => $this->refundableOrderDetailsQuery($orderIds, 'combo', $orderDetailTotals)
                    ->sum(DB::raw($this->allocatedOrderSql('orders.shipping_price'))),

                'total_buy_not_VAT'       => $this->refundableOrderDetailsQuery($orderIds, 'combo', $orderDetailTotals)
                    ->sum(DB::raw($this->allocatedOrderSql('orders.total_price_hoan_tien'))),

                'total_refund'            => DB::table('lich_su_giao_dich')
                    ->where('type', 2)
                    ->where('note', 'like', '%NH%M TI%U D%NG%')
                    ->where('note', 'not like', '%tự chọn%')
                    ->sum('amount'),

                'tuchon_qty'              => $this->refundableOrderDetailsQuery($orderIds, 'tuchon', $orderDetailTotals)
                    ->count(DB::raw('DISTINCT orders.id')),

                'tuchon_buy'              => $this->refundableOrderDetailsQuery($orderIds, 'tuchon', $orderDetailTotals)
                    ->sum(DB::raw($this->lineRevenueSql())),

                'tuchon_buy_price_vat'    => $this->refundableOrderDetailsQuery($orderIds, 'tuchon', $orderDetailTotals)
                    ->sum(DB::raw($this->allocatedOrderSql('orders.price_vat'))),

                'tuchon_buy_fee_shipping' => $this->refundableOrderDetailsQuery($orderIds, 'tuchon', $orderDetailTotals)
                    ->sum(DB::raw($this->allocatedOrderSql('orders.shipping_price'))),

                'tuchon_buy_not_VAT'      => $this->refundableOrderDetailsQuery($orderIds, 'tuchon', $orderDetailTotals)
                    ->sum(DB::raw($this->allocatedOrderSql('orders.total_price_hoan_tien') . ' - ' . $this->allocatedOrderSql('orders.price_vat'))),

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

            $customComboProductIdSql = $this->customComboProductIdSql();
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

            $categorySales = $this->refundableOrderDetailsQuery($orderIds, null, $orderDetailTotals)
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')

                ->select(
                    DB::raw($categoryNameSql . ' as category_name'),
                    DB::raw($productNameSql . ' as product_name'),
                    DB::raw($productIdSql . ' as product_id'),
                    DB::raw($this->comboQuantitySql() . ' as total_qty_order'),
                    DB::raw($this->lineRevenueSumSql() . ' as total_revenue'),
                    DB::raw("SUM(CASE WHEN {$this->costEligibleOrderSql()} THEN order_details.quantity * ({$purchasePriceCase}) ELSE 0 END) as total_cost"),
                    DB::raw($this->allocatedOrderSumSql('orders.price_vat') . ' as total_vat'),
                    DB::raw($this->allocatedOrderSumSql('orders.shipping_price') . ' as total_shipping'),
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

        } else {

            $topUsers      = [];
            $categorySales = [];

            $stats = \Cache::remember('user_dashboard_stats_' . $user->id, 300, function () use ($user) {
                $myOrders = Order::where('user_id', $user->id);
                return [
                    'my_orders'    => (clone $myOrders)->count(),
                    'my_pending'   => (clone $myOrders)->where('status', 'pending')->count(),
                    'my_completed' => (clone $myOrders)->where('status', 'completed')->count(),
                    'my_cancelled' => (clone $myOrders)->where('status', 'cancelled')->count(),
                ];
            });
        }

        return view('Core::Admin.dashboard.index', compact('stats', 'isAdmin', 'user', 'topUsers', 'categorySales', 'monthlyStats'));
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

    private function orderDetailTotalsQuery()
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

    private function refundableOrderDetailsQuery($orderIds, ?string $type, $orderDetailTotals)
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

    private function lineRevenueSql(): string
    {
        return 'COALESCE(order_details.quantity, 0) * COALESCE(order_details.price_product, 0)';
    }

    private function lineRevenueSumSql(): string
    {
        return 'SUM(' . $this->lineRevenueSql() . ')';
    }

    private function allocatedOrderSql(string $column): string
    {
        return 'CASE WHEN order_detail_totals.order_detail_revenue > 0 '
            . 'THEN COALESCE(' . $column . ', 0) * (' . $this->lineRevenueSql() . ') / order_detail_totals.order_detail_revenue '
            . 'ELSE 0 END';
    }

    private function allocatedOrderSumSql(string $column): string
    {
        return 'SUM(' . $this->allocatedOrderSql($column) . ')';
    }

    private function comboQuantitySql(): string
    {
        return "SUM(CASE WHEN orders.ma_don_hang LIKE 'TC%' THEN 0 ELSE COALESCE(order_details.quantity, 0) END)"
            . " + COUNT(DISTINCT CASE WHEN orders.ma_don_hang LIKE 'TC%' THEN orders.id END)";
    }

    private function costEligibleOrderSql(): string
    {
        return "(orders.ma_don_hang LIKE 'DH%' OR orders.ma_don_hang LIKE 'TC%')";
    }

    private function getPurchasePriceByProductId(array $purchasePrice): array
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

    private function getPurchasePriceCase(array $purchasePriceProductId): string
    {
        $case = 'CASE';

        foreach ($purchasePriceProductId as $productId => $price) {
            $case .= sprintf(' WHEN products.id = %d THEN %d', (int)$productId, (int)$price);
        }

        return $case . ' ELSE 0 END';
    }

    private function customComboProductIdSql(): string
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

    private function getCustomComboProductIds(): array
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

                $normalized = $this->normalizeSearchText($source);
                $rate = (float)($product->coCheHoanPhi?->tang_1 ?? 0);
                if ($rate == 40.0 || str_contains($normalized, '40') || str_contains($normalized, 'lien ket') || str_contains($normalized, 'splk')) {
                    $map['tc_splk_hp40'] = (int)$product->id;
                }
            });

        return $map;
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
        $normalized = $this->normalizeSearchText($source);
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
        $normalized = $this->normalizeSearchText((string)$text);

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

    private function normalizeSearchText(string $text): string
    {
        $normalized = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

        return strtolower($normalized !== false ? $normalized : $text);
    }

}
