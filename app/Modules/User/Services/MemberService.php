<?php

namespace App\Modules\User\Services;

use App\Models\User;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Thành viên cấp dưới và khách hàng trực tiếp của một user.
 */
class MemberService
{
    public const PER_PAGE = 20;

    public function __construct(private readonly UserRepository $users)
    {
    }

    public function directMembers(User $user): Collection
    {
        return $this->users->membersByIds($this->users->directChildIds($user->id));
    }

    /** Chỉ xem được chính mình, người trong nhánh của mình, hoặc có quyền user.view */
    public function canViewChildrenOf(User $viewer, int $userId): bool
    {
        return $viewer->id === $userId
            || $this->users->isDescendant($viewer->id, $userId)
            || $viewer->hasPermission('user.view');
    }

    public function childrenPayload(int $userId): Collection
    {
        $columns = ['id', 'name', 'email', 'tel', 'vi_tien', 'active', 'created_at', 'image'];

        return $this->users->membersByIds($this->users->directChildIds($userId), $columns)
            ->map(fn (User $m) => [
                'id'           => $m->id,
                'name'         => $m->name,
                'email'        => $m->email,
                'tel'          => $m->tel,
                'vi_tien'      => number_format($m->vi_tien ?? 0, 0, ',', '.') . '₫',
                'active'       => $m->active,
                'direct_count' => $m->direct_count,
                'joined'       => $m->created_at?->format('d/m/Y'),
                'avatar_char'  => strtoupper(substr($m->name, 0, 1)),
                'image'        => $m->image ? cdn($m->image) : null,
            ]);
    }

    /** Đếm lại số thành viên giới thiệu đã tham gia của toàn bộ user */
    public function recountReferrals(): void
    {
        User::chunkById(500, function ($users) {
            foreach ($users as $user) {
                $user->so_thanh_vien_gt = User::where('invite_by', $user->id)->whereNotNull('ngay_tham_gia')->count();
                $user->save();
            }
        });
    }

    public function referrals(User $user): Collection
    {
        return $this->users->referralsWithMonthlyPurchase($user);
    }

    /**
     * Khách hàng trực tiếp chưa mua hàng: người đăng ký bằng mã giới thiệu của $user
     * (invite_by = id) nhưng chưa có đơn đầu tiên (ngay_tham_gia chỉ được gán khi mua lần đầu).
     */
    public function unpurchasedReferrals(User $user, array $filters): LengthAwarePaginator
    {
        $search = trim((string) ($filters['search'] ?? ''));

        return $this->users->paginateUnpurchasedReferrals($user, $search, self::PER_PAGE)
            ->through(fn (User $member) => [
                'id'         => $member->id,
                'name'       => $member->name,
                'tel'        => $member->tel,
                'created_at' => $member->created_at?->format('Y-m-d H:i:s'), // Ngày đăng ký
            ]);
    }
}
