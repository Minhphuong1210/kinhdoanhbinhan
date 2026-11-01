<?php

namespace App\Modules\Finance\Services;

use App\Models\LichSuGiaoDich;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Thu nhập trong tháng của user theo 6 nhóm tuyến dưới.
 */
class IncomeService
{
    private const LEVELS = [1, 2, 3, 4, 5, 6];

    private const SETTING_KEYS = [
        1 => 'hoan_phi_nhom_mot',
        2 => 'hoan_phi_nhom_hai',
        3 => 'hoan_phi_nhom_ba',
        4 => 'hoan_phi_nhom_bon',
        5 => 'hoan_phi_nhom_nam',
        6 => 'hoan_phi_nhom_sau',
    ];

    public function monthlyData(User $user): array
    {
        return Cache::remember("thu_nhap_user_{$user->id}_" . now()->format('Y_m'), 30, fn () => $this->build($user));
    }

    private function build(User $user): array
    {
        $month = [Carbon::now()->startOfMonth()->startOfDay(), Carbon::now()->endOfMonth()->endOfDay()];
        $hierarchy = DB::table('admin_hierarchy')->where('ancestor_id', $user->id);
        $branch = (clone $hierarchy)->whereBetween('depth', [1, 6]);

        $income = LichSuGiaoDich::where('admin_id', $user->id)
            ->whereBetween('created_at', $month)
            ->where('type', 2);

        $doanhSoNhom = [];
        foreach (self::LEVELS as $level) {
            $doanhSoNhom[(string) $level] = (clone $income)
                ->whereIn('sender_id', (clone $hierarchy)->where('depth', $level)->pluck('descendant_id')->toArray())
                ->where('note', 'like', '%NHÓM TIÊU DÙNG%')
                ->sum('amount');
        }

        $descendantIds = (clone $branch)->pluck('descendant_id')->toArray();
        $branchOrders = Order::whereIn('user_id', $descendantIds)
            ->whereBetween('created_at', $month)
            ->where('status', 'completed');

        $byLevel = DB::table('orders')
            ->join('admin_hierarchy', 'orders.user_id', '=', 'admin_hierarchy.descendant_id')
            ->where('admin_hierarchy.ancestor_id', $user->id)
            ->whereBetween('orders.created_at', $month)
            ->whereBetween('admin_hierarchy.depth', [1, 6])
            ->where('orders.status', 'completed')
            ->groupBy('admin_hierarchy.depth');

        return [
            'settings'               => Setting::whereIn('name', array_values(self::SETTING_KEYS))->pluck('value', 'name')->toArray(),
            'settingKeys'            => self::SETTING_KEYS,
            'descendants'            => (clone $branch)
                ->select('depth', DB::raw('COUNT(descendant_id) as so_thanh_vien'))
                ->groupBy('depth')
                ->pluck('so_thanh_vien', 'depth')
                ->toArray(),
            'doanh_so_nhom_chung'    => $doanhSoNhom,
            'hoan_tien_f1'           => (clone $income)->where('note', 'like', '%trực tiếp%')->sum('amount'),
            'doanh_so_nhanh'         => (clone $branchOrders)->sum('total_price_hoan_tien'),
            'sltv_new'               => (clone $branchOrders)->where('first_order', 1)->distinct('user_id')->count('user_id'),
            'thanh_vien_mua_hang'    => (clone $byLevel)
                ->select('admin_hierarchy.depth', DB::raw('COUNT(DISTINCT orders.user_id) as so_tv_mua'))
                ->pluck('so_tv_mua', 'admin_hierarchy.depth')
                ->toArray(),
            'so_luong_mua_theo_nhom' => (clone $byLevel)
                ->select('admin_hierarchy.depth', DB::raw('COUNT(orders.id) as so_don'))
                ->pluck('so_don', 'admin_hierarchy.depth')
                ->toArray(),
            'tong_diem'              => (clone $income)->whereIn('sender_id', $descendantIds)->sum('amount'),
        ];
    }
}
