<?php

namespace App\Modules\Core\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LichSuGiaoDich;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $data = Cache::remember("thu_nhap_user_{$user->id}_" . now()->format('Y_m'), 30, function () use ($user) {

            $startOfMonth = Carbon::now()->startOfMonth()->startOfDay();
            $endOfMonth   = Carbon::now()->endOfMonth()->endOfDay();

            // ---- Settings % hoàn phí ----
            $settingKeys = [
                1 => 'hoan_phi_nhom_mot',
                2 => 'hoan_phi_nhom_hai',
                3 => 'hoan_phi_nhom_ba',
                4 => 'hoan_phi_nhom_bon',
                5 => 'hoan_phi_nhom_nam',
                6 => 'hoan_phi_nhom_sau',
            ];
            $settings = Setting::whereIn('name', array_values($settingKeys))
                ->pluck('value', 'name')
                ->toArray();

            // ---- Số thành viên theo từng cấp (từ closure table) ----
            $descendants = DB::table('admin_hierarchy')
                ->where('ancestor_id', $user->id)
                ->whereBetween('depth', [1, 6])
                ->select('depth', DB::raw('COUNT(descendant_id) as so_thanh_vien'))
                ->groupBy('depth')
                ->pluck('so_thanh_vien', 'depth')
                ->toArray();

            // ---- Tổng tiền hoàn theo nhóm (join orders + admin_hierarchy) ----
//            $hoan_tien_groups = DB::table('orders')
//                ->join('admin_hierarchy', 'orders.user_id', '=', 'admin_hierarchy.descendant_id')
//                ->where('admin_hierarchy.ancestor_id', $user->id)
//                ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
//                ->whereBetween('admin_hierarchy.depth', [1, 6])
//                ->where('orders.status', 'completed')
//                ->groupBy('admin_hierarchy.depth')
//                ->select('admin_hierarchy.depth', DB::raw('SUM(orders.total_price_hoan_tien) as tong_hoan_tien'))
//                ->pluck('tong_hoan_tien', 'depth')
//                ->toArray();





//            dd($hoan_tien_groups);

            // ---- Hoàn tiền F1 (từ LichSuGiaoDich — type=2, người gửi là F1) ----
            $f1Ids = DB::table('admin_hierarchy')
                ->where('ancestor_id', $user->id)
                ->where('depth', 1)
                ->pluck('descendant_id');


//            dd($f1Ids);

            $hoan_tien_f1 = LichSuGiaoDich::where('admin_id', $user->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('type', 2)
//                ->whereIn('sender_id', $f1Ids)
                ->where('note', 'like', '%trực tiếp%')
//                ->count();
                ->sum('amount');

//            dd($hoan_tien_f1);


            // ---- Tất cả descendant IDs (depth 1-6) ----
            $descendants_ids = DB::table('admin_hierarchy')
                ->where('ancestor_id', $user->id)
                ->whereBetween('depth', [1, 6])
                ->pluck('descendant_id')
                ->toArray();


// lấy nhóm 1
            $descendants_ids_nhom_1 = DB::table('admin_hierarchy')
                ->where('ancestor_id', $user->id)
                ->where('depth',1)
                ->pluck('descendant_id')
                ->toArray();

            $descendants_ids_nhom_2 = DB::table('admin_hierarchy')
                ->where('ancestor_id', $user->id)
                ->where('depth',2)
                ->pluck('descendant_id')
                ->toArray();

            $descendants_ids_nhom_3 = DB::table('admin_hierarchy')
                ->where('ancestor_id', $user->id)
                ->where('depth',3)
                ->pluck('descendant_id')
                ->toArray();

            $descendants_ids_nhom_4 = DB::table('admin_hierarchy')
                ->where('ancestor_id', $user->id)
                ->where('depth',4)
                ->pluck('descendant_id')
                ->toArray();
            $descendants_ids_nhom_5 = DB::table('admin_hierarchy')
                ->where('ancestor_id', $user->id)
                ->where('depth',5)
                ->pluck('descendant_id')
                ->toArray();
            $descendants_ids_nhom_6 = DB::table('admin_hierarchy')
                ->where('ancestor_id', $user->id)
                ->where('depth',6)
                ->pluck('descendant_id')
                ->toArray();

            $doanh_so_nhom_1 =  LichSuGiaoDich::where('admin_id', $user->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('type', 2)
                ->whereIn('sender_id', $descendants_ids_nhom_1)
                ->where('note', 'like', '%NHÓM TIÊU DÙNG%')
                ->sum('amount');


            $doanh_so_nhom_2 =  LichSuGiaoDich::where('admin_id', $user->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('type', 2)
                ->whereIn('sender_id', $descendants_ids_nhom_2)
                ->where('note', 'like', '%NHÓM TIÊU DÙNG%')
                ->sum('amount');

            $doanh_so_nhom_3 =  LichSuGiaoDich::where('admin_id', $user->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('type', 2)
                ->whereIn('sender_id', $descendants_ids_nhom_3)
                ->where('note', 'like', '%NHÓM TIÊU DÙNG%')
                ->sum('amount');

            $doanh_so_nhom_4 =  LichSuGiaoDich::where('admin_id', $user->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('type', 2)
                ->whereIn('sender_id', $descendants_ids_nhom_4)
                ->where('note', 'like', '%NHÓM TIÊU DÙNG%')
                ->sum('amount');

            $doanh_so_nhom_5 =  LichSuGiaoDich::where('admin_id', $user->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('type', 2)
                ->whereIn('sender_id', $descendants_ids_nhom_5)
                ->where('note', 'like', '%NHÓM TIÊU DÙNG%')
                ->sum('amount');

            $doanh_so_nhom_6 =  LichSuGiaoDich::where('admin_id', $user->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('type', 2)
                ->whereIn('sender_id', $descendants_ids_nhom_6)
                ->where('note', 'like', '%NHÓM TIÊU DÙNG%')
                ->sum('amount');

            $doanh_so_nhom_chung= [
                '1'=>$doanh_so_nhom_1,
                '2'=>$doanh_so_nhom_2,
                '3'=>$doanh_so_nhom_3,
                '4'=>$doanh_so_nhom_4,
                '5'=>$doanh_so_nhom_5,
                '6'=>$doanh_so_nhom_6,
            ];




            // ---- Doanh số nhánh (tổng hoan_tien các đơn completed trong tháng) ----
            $doanh_so_nhanh = Order::whereIn('user_id', $descendants_ids)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('status', 'completed')
                ->sum('total_price_hoan_tien');

            // ---- Số thành viên mới trong tháng (first_order) ----
            $sltv_new = Order::whereIn('user_id', $descendants_ids)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('status', 'completed')
                ->where('first_order', 1)
                ->distinct('user_id')
                ->count('user_id');

            // ---- Số thành viên đã mua hàng theo nhóm ----
            $thanh_vien_mua_hang = DB::table('orders')
                ->join('admin_hierarchy', 'orders.user_id', '=', 'admin_hierarchy.descendant_id')
                ->where('admin_hierarchy.ancestor_id', $user->id)
                ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
                ->whereBetween('admin_hierarchy.depth', [1, 6])
                ->where('orders.status', 'completed')
                ->groupBy('admin_hierarchy.depth')
                ->select('admin_hierarchy.depth', DB::raw('COUNT(DISTINCT orders.user_id) as so_tv_mua'))
                ->pluck('so_tv_mua', 'admin_hierarchy.depth')
                ->toArray();

            // ---- Số lượng đơn mua theo nhóm ----
            $so_luong_mua_theo_nhom = DB::table('orders')
                ->join('admin_hierarchy', 'orders.user_id', '=', 'admin_hierarchy.descendant_id')
                ->where('admin_hierarchy.ancestor_id', $user->id)
                ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
                ->whereBetween('admin_hierarchy.depth', [1, 6])
                ->where('orders.status', 'completed')
                ->groupBy('admin_hierarchy.depth')
                ->select('admin_hierarchy.depth', DB::raw('COUNT(orders.id) as so_don'))
                ->pluck('so_don', 'admin_hierarchy.depth')
                ->toArray();

            $tong_diem =  LichSuGiaoDich::where('admin_id', $user->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('type', 2)
                ->whereIn('sender_id', $descendants_ids)

                ->sum('amount');

            return compact(
                'settings', 'settingKeys', 'descendants',
                'doanh_so_nhom_chung', 'hoan_tien_f1',
                'doanh_so_nhanh', 'sltv_new',
                'thanh_vien_mua_hang', 'so_luong_mua_theo_nhom','tong_diem'
            );
        });

        return view('Core::Admin.income.index', $data);
    }
}
