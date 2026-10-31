<?php

namespace App\Modules\Core\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;

class TreeController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Lấy toàn bộ user trong nhánh của mình (descendants qua parent_id)
        $allUsers = User::whereNotNull('parent_id')->orderBy('ngay_tham_gia', 'ASC')->get();


//        $directChildIds = DB::table('admin_hierarchy')
//            ->where('ancestor_id', $user->id)
//            ->where('depth', 1)
//            ->pluck('descendant_id');

        // Gom nhóm theo parent_id để duyệt nhanh
        $grouped = [];
        foreach ($allUsers as $u) {
            $grouped[$u->parent_id][] = $u;
        }

        // Tính số ngày từ lần mua đơn cuối
        $daysSince = function (?string $date): int {
            if (!$date) return 0;
            return (int) Carbon::parse($date)->diffInDays(now());
        };

        // Phân loại class dựa trên số thành viên giới thiệu
        $nodeClass = function (User $u): string {
            $count = $u->so_thanh_vien_gt ?? 0;
            if ($count >= 10) return 'level-10';
            if ($count >= 5)  return 'level-5';
            if ($count >= 3)  return 'level-3';
            return 'level-0';
        };

        // Đệ quy xây cây
        $maxDepth = 6;

        $buildTree = function (int $parentId, int $depth = 1) use (&$buildTree, $grouped, $daysSince, $nodeClass, $maxDepth): array {
            // Nếu vượt quá 6 tầng thì dừng
            if ($depth > $maxDepth) {
                return [];
            }

            $nodes = [];

            foreach ($grouped[$parentId] ?? [] as $u) {
                $nodes[] = [
                    'name'      => $u->name . '<br><small>' . $u->tel . '</small>',
                    'title'     => number_format($u->vi_tien ?? 0, 0, ',', '.') . ' VNĐ',
                    'date'      => $daysSince($u->ngay_mua_don_cuoi) . ' ngày',
                    'className' => $nodeClass($u),
                    'children'  => $buildTree($u->id, $depth + 1), // tăng depth
                ];
            }

            return $nodes;
        };

        // Root = user hiện tại
        $treeData = [
            'name'      => $user->name . '<br><small>' . $user->tel . '</small>' ,
            'title'     => number_format($user->vi_tien ?? 0, 0, ',', '.') . ' VNĐ'  ,
            'date'      => $daysSince($user->ngay_mua_don_cuoi) . ' ngày',
            'className' => 'level-root',
            'children'  => $buildTree($user->id),
        ];

        return view('Core::Admin.tree.index', compact('treeData', 'user'));
    }
}
