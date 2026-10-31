<?php

namespace App\Modules\Core\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Lấy danh sách thành viên cấp dưới trực tiếp (depth = 1 từ closure table)
        $directChildIds = DB::table('admin_hierarchy')
            ->where('ancestor_id', $user->id)
            ->where('depth', 1)
            ->pluck('descendant_id');

        // Nếu bảng rỗng hoặc không có data, fallback về parent_id
        if ($directChildIds->isEmpty()) {
            $directChildIds = User::where('parent_id', $user->id)->pluck('id');
        }

        $members = User::whereIn('id', $directChildIds)
            ->withCount(['children as direct_count'])
            ->orderBy('name')
            ->get();

        return view('Core::Admin.members.index', compact('members', 'user'));
    }

    /**
     * AJAX: lấy danh sách con trực tiếp của một user
     */
    public function children(int $userId)
    {
        // Kiểm tra chỉ admin hoặc user cha của userId được gọi
        $authUser = auth()->user();

        // Lấy con trực tiếp từ closure table
        $childIds = DB::table('admin_hierarchy')
            ->where('ancestor_id', $userId)
            ->where('depth', 1)
            ->pluck('descendant_id');

        // Fallback về parent_id nếu closure table chưa có data
        if ($childIds->isEmpty()) {
            $childIds = User::where('parent_id', $userId)->pluck('id');
        }

        $children = User::whereIn('id', $childIds)
            ->withCount(['children as direct_count'])
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'tel', 'vi_tien', 'active', 'created_at', 'image']);

        return response()->json($children->map(fn($m) => [
            'id'           => $m->id,
            'name'         => $m->name,
            'email'        => $m->email,
            'tel'          => $m->tel,
            'vi_tien'      => number_format($m->vi_tien ?? 0, 0, ',', '.') . '₫',
            'active'       => $m->active,
            'direct_count' => $m->direct_count,
            'joined'       => $m->created_at?->format('d/m/Y'),
            'avatar_char'  => strtoupper(substr($m->name, 0, 1)),
            'image'        => $m->image ? cdn($m->image)  : null,
        ]));
    }

    /**
     * Danh sách khách hàng trực tiếp — những người nhập mã giới thiệu của mình
     */
    public function myReferrals()
    {
        $user = auth()->user();

        // Tìm theo SĐT hoặc ID của user hiện tại
        $referrals = User::Where('invite_by', (string) $user->id)->whereNotNull('ngay_tham_gia')
            ->orderBy('name')
            ->get();

        // Đánh dấu ai đã mua gói trong tháng này
        $thisMonth = now()->month;
        $thisYear  = now()->year;

        $referrals = $referrals->map(function ($member) use ($thisMonth, $thisYear) {
            $member->bought_this_month = \App\Models\Order::where('user_id', $member->id)
                ->where('status', 'completed')
                ->whereMonth('created_at', $thisMonth)
                ->whereYear('created_at', $thisYear)
                ->exists();
            return $member;
        });

        return view('Core::Admin.members.referrals', compact('referrals', 'user'));
    }
}
