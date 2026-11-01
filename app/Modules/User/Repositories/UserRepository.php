<?php

namespace App\Modules\User\Repositories;

use App\Models\Order;
use App\Models\User;
use App\Modules\Shared\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository
{
    protected string $model = User::class;

    /** Tìm theo email hoặc SĐT */
    public function findByLogin(string $login): ?User
    {
        $column = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'tel';

        return $this->query()->where($column, $login)->first();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->query()->where('email', $email)->first();
    }

    /** Mã giới thiệu có thể là ID, email hoặc SĐT */
    public function findByReferralCode(string $code): ?User
    {
        return $this->query()
            ->where('id', $code)
            ->orWhere('email', $code)
            ->orWhere('tel', $code)
            ->first();
    }

    public function paginateForAdmin(array $filters): LengthAwarePaginator
    {
        $query = $this->query()->with('dangKyXacNhanHtx');

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('tel', 'like', "%$search%");
            });
        }

        foreach (['active', 'is_admin'] as $column) {
            if (($filters[$column] ?? '') !== '' && isset($filters[$column])) {
                $query->where($column, $filters[$column]);
            }
        }

        if (!empty($filters['tu_ngay'])) {
            $query->where('created_at', '>=', Carbon::parse($filters['tu_ngay'])->startOfDay());
        }

        if (!empty($filters['den_ngay'])) {
            $query->where('created_at', '<=', Carbon::parse($filters['den_ngay'])->endOfDay());
        }

        return $query->latest()->paginate(20)->withQueryString();
    }

    public function search(?string $term, int $limit = 20): Collection
    {
        return $this->query()
            ->when($term, function ($query) use ($term) {
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%$term%")
                        ->orWhere('email', 'like', "%$term%")
                        ->orWhere('tel', 'like', "%$term%");
                });
            })
            ->select('id', 'name', 'email', 'tel')
            ->limit($limit)
            ->get();
    }

    public function parentOptions(?int $exceptId = null): Collection
    {
        return $this->query()
            ->select('id', 'name', 'email')
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->orderBy('name')
            ->get();
    }

    /** ID con trực tiếp: closure table, chưa có dữ liệu thì dùng parent_id */
    public function directChildIds(int $userId): Collection
    {
        $ids = DB::table('admin_hierarchy')
            ->where('ancestor_id', $userId)
            ->where('depth', 1)
            ->pluck('descendant_id');

        return $ids->isEmpty()
            ? $this->query()->where('parent_id', $userId)->pluck('id')
            : $ids;
    }

    public function membersByIds(Collection $ids, array $columns = ['*']): Collection
    {
        return $this->query()
            ->whereIn('id', $ids)
            ->withCount(['children as direct_count'])
            ->orderBy('name')
            ->get($columns);
    }

    public function isDescendant(int $ancestorId, int $userId): bool
    {
        return DB::table('admin_hierarchy')
            ->where('ancestor_id', $ancestorId)
            ->where('descendant_id', $userId)
            ->exists();
    }

    /** Khách hàng trực tiếp đã tham gia, kèm cờ bought_this_month */
    public function referralsWithMonthlyPurchase(User $user): Collection
    {
        return $this->query()
            ->where('invite_by', (string) $user->id)
            ->whereNotNull('ngay_tham_gia')
            ->orderBy('name')
            ->get()
            ->map(function (User $member) {
                $member->bought_this_month = Order::where('user_id', $member->id)
                    ->where('status', 'completed')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->exists();

                return $member;
            });
    }

    /** Khách hàng trực tiếp chưa mua hàng */
    public function paginateUnpurchasedReferrals(User $user, string $search, int $perPage): LengthAwarePaginator
    {
        return $this->query()
            ->where('invite_by', (string) $user->id)
            ->whereNull('ngay_tham_gia')
            ->whereDoesntHave('orders')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('tel', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->select(['id', 'name', 'tel', 'created_at'])
            ->paginate($perPage)
            ->withQueryString();
    }

    /** Toàn bộ user đã vào cây, gom theo parent_id */
    public function treeUsersGroupedByParent(array $columns = ['*']): Collection
    {
        return $this->query()
            ->select($columns)
            ->whereNotNull('parent_id')
            ->orderBy('ngay_tham_gia', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->groupBy('parent_id');
    }
}
