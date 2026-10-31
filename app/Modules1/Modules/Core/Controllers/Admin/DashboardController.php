<?php

namespace App\Modules\Core\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $user    = auth()->user();
        $isAdmin = $user->is_admin;
        $monthlyStats=null;
        $monthlyRefund=null;
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


            // ===== THỐNG KÊ THEO THÁNG =====
            $monthlyStats = DB::table('orders')
                ->select(
                    DB::raw('DATE_FORMAT(orders.created_at, "%Y-%m") as month'),
                    DB::raw('SUM(CASE WHEN orders.ma_don_hang LIKE "DH%" THEN 1 ELSE 0 END) as total_combo'),
                    DB::raw('SUM(CASE WHEN orders.status = "completed" THEN orders.total_price ELSE 0 END) as total_buy')
                )
                ->when($fromDate, fn($q) => $q->whereDate('orders.created_at', '>=', $fromDate))
                ->when($toDate, fn($q) => $q->whereDate('orders.created_at', '<=', $toDate))
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->get();

            $monthlyRefund = DB::table('lich_su_giao_dich')
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('SUM(amount) as total_refund')
                )
                ->where('type', 2)
                ->where('note', 'like', '%NHÓM TIÊU DÙNG%')
                ->when($fromDate, fn($q) => $q->whereDate('created_at', '>=', $fromDate))
                ->when($toDate, fn($q) => $q->whereDate('created_at', '<=', $toDate))
                ->groupBy('month')
                ->get()
                ->keyBy('month');


            foreach ($monthlyStats as $item) {
                $item->total_refund = $monthlyRefund[$item->month]->total_refund ?? 0;
            }

            $stats = [
                'products'   => Product::count(),
                'orders'     => Order::whereIn('id', $orderIds)->count(),
                'users'      => User::count(),
                'categories' => Category::count(),

                'revenue' => Order::whereIn('id', $orderIds)
                    ->where('status', 'completed')
                    ->sum('total_price'),

                'pending' => Order::whereIn('id', $orderIds)
                    ->where('status', 'pending')
                    ->count(),

                'total_qty' => DB::table('order_details')
                    ->join('orders', 'order_details.order_id', '=', 'orders.id')
                    ->whereIn('orders.id', $orderIds)
                    ->where('orders.status', 'completed')
                    ->where('orders.ma_don_hang', 'NOT LIKE', 'TC%')
                    ->where('orders.name', 'LIKE', 'COMBO%')
                    ->where('order_details.the_loai', 'LIKE', 'product%')
                    ->sum('order_details.quantity'),

//                'total_buy' => DB::table('order_details')
//                    ->join('orders', 'order_details.order_id', '=', 'orders.id')
//                    ->whereIn('orders.id', $orderIds)
//                    ->where('orders.status', 'completed')
//                    ->where('orders.ma_don_hang', 'NOT LIKE', 'TC%')
//                    ->where('orders.name', 'LIKE', 'COMBO%')
//                    ->sum(DB::raw('order_details.quantity * order_details.price_product')),


                'total_buy' => DB::table('orders')
                    ->whereIn('orders.id', $orderIds)
                    ->where('orders.ma_don_hang', 'NOT LIKE', 'TC%')
                    ->where('orders.name', 'LIKE', 'COMBO%')
                    ->sum('total_price'),

                'total_buy_price_vat' => DB::table('orders')
                    ->whereIn('orders.id', $orderIds)
                    ->where('orders.ma_don_hang', 'NOT LIKE', 'TC%')
                    ->where('orders.name', 'LIKE', 'COMBO%')
                    ->sum('price_vat'),

                'total_buy_fee_shipping' => DB::table('orders')
                    ->whereIn('orders.id', $orderIds)
                    ->where('orders.ma_don_hang', 'NOT LIKE', 'TC%')
                    ->where('orders.name', 'LIKE', 'COMBO%')
                    ->sum('shipping_price'),

                'total_buy_not_VAT' => DB::table('orders')
                    ->whereIn('orders.id', $orderIds)
                    ->where('orders.ma_don_hang', 'NOT LIKE', 'TC%')
                    ->where('orders.name', 'LIKE', 'COMBO%')
                    ->sum('total_price_hoan_tien'),

                'total_refund' => DB::table('lich_su_giao_dich')
                    ->where('type', 2)
                    ->where('note', 'like', '%NHÓM TIÊU DÙNG%')
                    ->where('note', 'not like', '%tự chọn%')
                    ->sum('amount'),

                'tuchon_qty' => DB::table('orders')
                    ->whereIn('orders.id', $orderIds)
                    ->where('orders.ma_don_hang', 'LIKE', 'TC%')
                    ->count(),

                'tuchon_buy' => DB::table('orders')
                    ->whereIn('orders.id', $orderIds)
                    ->where('orders.ma_don_hang', 'LIKE', 'TC%')
                    ->sum('total_price'),


                'tuchon_buy_price_vat' => DB::table('orders')
                    ->whereIn('orders.id', $orderIds)
                    ->where('orders.ma_don_hang', 'LIKE', 'TC%')
                    ->sum('price_vat'),

                'tuchon_buy_fee_shipping' => DB::table('orders')
                    ->whereIn('orders.id', $orderIds)
                    ->where('orders.ma_don_hang', 'LIKE', 'TC%')
                    ->sum('shipping_price'),

                'tuchon_buy_not_VAT' => DB::table('orders')
                    ->whereIn('orders.id', $orderIds)
                    ->where('orders.ma_don_hang', 'LIKE', 'TC%')
                    ->sum(DB::raw('total_price_hoan_tien - price_vat')),



//                'tuchon_buy' => DB::table('order_details')
//                    ->join('orders', 'order_details.order_id', '=', 'orders.id')
//                    ->whereIn('orders.id', $orderIds)
//                    ->where('orders.status', 'completed')
//                    ->where('orders.ma_don_hang', 'LIKE', 'TC%')
//                    ->sum(DB::raw('order_details.quantity * order_details.price_product')),

                'tuchon_refund' => DB::table('lich_su_giao_dich')
                    ->where('type', 2)
                    ->where('note', 'like', '%NHÓM TIÊU DÙNG%')
                    ->where('note', 'like', '%tự chọn%')
                    ->sum('amount'),
            ];


            $stats['so_tien_rut_tru_thue'] = DB::table('yeu_cau_rut_tien')->where('status', 1)->sum('so_tien_sau_thue');
            $stats['so_tien_da_rut'] = DB::table('yeu_cau_rut_tien')->where('status', 1)->sum('so_tien');
            $stats['so_tien_da_tra_thuc_te'] = $stats['so_tien_da_rut'] - $stats['so_tien_rut_tru_thue'];

            $stats['so_tien_thanh_vien_chưa_du_3_f1'] = User::where('so_thanh_vien_gt', '<', 3)
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


            $categorySales = DB::table('order_details')
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->join('products', 'order_details.product_id', '=', 'products.id')
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')

                ->whereIn('orders.id', $orderIds)
                ->where('orders.status', 'completed')

                ->select(
                    'categories.name as category_name',
                    'products.name as product_name',
                    DB::raw('SUM(order_details.quantity) as total_qty_order'),
                    DB::raw('SUM(order_details.quantity * order_details.price_product) as total_revenue'),
                    DB::raw('0 as total_refund')
                )

                ->groupBy(
                    'categories.name',
                    'products.name'
                )
                ->orderByDesc('total_revenue')
                ->get();

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
    }}
