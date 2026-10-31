<?php

namespace App\Modules\Core\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MemberApiService
{
    public const PER_PAGE = 20;

    /**
     * Khách hàng trực tiếp chưa mua hàng: người đăng ký bằng mã giới thiệu của $user
     * (invite_by = id) nhưng chưa có đơn đầu tiên (ngay_tham_gia chỉ được gán khi mua lần đầu).
     */
    public function khachHangTrucTiepChuaMua(User $user, array $filters): LengthAwarePaginator
    {
        $search = trim((string) ($filters['search'] ?? ''));

        return User::query()
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
            ->paginate(self::PER_PAGE)
            ->withQueryString()
            ->through(fn (User $member) => [
                'id'         => $member->id,
                'name'       => $member->name,
                'tel'        => $member->tel,
                'created_at' => $member->created_at?->format('Y-m-d H:i:s'), // Ngày đăng ký
            ]);
    }
}
