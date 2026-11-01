<?php

namespace App\Modules\User\Services;

use App\Models\User;
use App\Modules\User\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Sơ đồ tổ chức: dữ liệu cho trang cây ở web và API organization-chart.
 */
class MemberTreeService
{
    private const WEB_MAX_DEPTH = 6;

    private const CHART_COLUMNS = [
        'id', 'name', 'email', 'tel', 'parent_id', 'invite_by', 'image', 'active', 'vi_tien',
        'vi_tien_mua_hang', 'ngay_tham_gia', 'ngay_mua_don_cuoi', 'so_thanh_vien_gt', 'level',
    ];

    public function __construct(private readonly UserRepository $users)
    {
    }

    /** Dữ liệu OrgChart. name được render dạng HTML nên phải escape */
    public function webTree(User $user): array
    {
        return $this->webNode($user, 0, $this->users->treeUsersGroupedByParent(), 'level-root');
    }

    public function organizationChart(User $user, int $maxDepth): array
    {
        return $this->chartNode($user, $this->users->treeUsersGroupedByParent(self::CHART_COLUMNS), 0, $maxDepth, true);
    }

    private function webNode(User $user, int $depth, Collection $grouped, ?string $className = null): array
    {
        $children = $depth < self::WEB_MAX_DEPTH ? ($grouped->get($user->id) ?? collect()) : collect();

        return [
            'name'      => e($user->name) . '<br><small>' . e($user->tel) . '</small>',
            'title'     => number_format($user->vi_tien ?? 0, 0, ',', '.') . ' VNĐ',
            'date'      => $this->daysSince($user->ngay_mua_don_cuoi) . ' ngày',
            'className' => $className ?? $this->nodeClass($user),
            'children'  => $children->map(fn (User $child) => $this->webNode($child, $depth + 1, $grouped))->values()->all(),
        ];
    }

    private function chartNode(User $user, Collection $grouped, int $depth, int $maxDepth, bool $isRoot = false): array
    {
        $allChildren = $grouped->get($user->id) ?? collect();
        $children = $depth >= $maxDepth ? collect() : $allChildren;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'tel' => $user->tel,
            'parent_id' => $user->parent_id,
            'invite_by' => $user->invite_by,
            'image' => asset_url($user->image),
            'active' => (int) $user->active,
            'level' => $user->level,
            'depth' => $depth,
            'vi_tien' => (float) ($user->vi_tien ?? 0),
            'vi_tien_mua_hang' => (float) ($user->vi_tien_mua_hang ?? 0),
            'so_thanh_vien_gt' => (int) ($user->so_thanh_vien_gt ?? 0),
            'direct_children_count' => $allChildren->count(),
            'ngay_tham_gia' => $this->dateTimeString($user->ngay_tham_gia),
            'ngay_mua_don_cuoi' => $this->dateTimeString($user->ngay_mua_don_cuoi),
            'days_since_last_order' => $this->daysSince($user->ngay_mua_don_cuoi),
            'display' => [
                'title' => number_format($user->vi_tien ?? 0, 0, ',', '.') . ' VND',
                'date' => $this->daysSince($user->ngay_mua_don_cuoi) . ' ngay',
                'class_name' => $isRoot ? 'level-root' : $this->nodeClass($user),
            ],
            'children' => $children
                ->map(fn (User $child) => $this->chartNode($child, $grouped, $depth + 1, $maxDepth))
                ->values()
                ->all(),
        ];
    }

    /** Phân loại theo số thành viên giới thiệu */
    private function nodeClass(User $user): string
    {
        $count = (int) ($user->so_thanh_vien_gt ?? 0);

        if ($count >= 10) {
            return 'level-10';
        }

        if ($count >= 5) {
            return 'level-5';
        }

        if ($count >= 3) {
            return 'level-3';
        }

        return 'level-0';
    }

    private function daysSince($date): int
    {
        return $date ? (int) Carbon::parse($date)->diffInDays(now()) : 0;
    }

    private function dateTimeString($date): ?string
    {
        return $date ? Carbon::parse($date)->format('Y-m-d H:i:s') : null;
    }
}
