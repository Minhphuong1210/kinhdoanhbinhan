<?php

namespace App\Modules\Notifycation\Repositories;

use App\Models\Notification;
use App\Models\NotificationReads;
use App\Models\User;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class NotificationRepository extends BaseRepository
{
    protected string $model = Notification::class;

    public function paginateBroadcasts(array $filters): LengthAwarePaginator
    {
        return $this->query()
            ->broadcast()
            ->when($filters['vi_tri'] ?? null, fn ($q, $viTri) => $q->where('vi_tri', $viTri))
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->withCount('reads')
            ->latest()
            ->paginate(20)
            ->withQueryString();
    }

    /** Thông báo user được xem trên website đang chạy */
    public function visibleTo(User $user): Builder
    {
        return $this->query()->visibleTo($user)->forCurrentSite();
    }

    public function visibleWithReads(User $user): Builder
    {
        return $this->visibleTo($user)->with(['reads' => fn ($q) => $q->where('user_id', $user->id)]);
    }

    public function countUnread(User $user): int
    {
        return $this->visibleTo($user)
            ->whereDoesntHave('reads', fn ($q) => $q->where('user_id', $user->id))
            ->count();
    }

    public function findOrCreateRead(Notification $notification, User $user): NotificationReads
    {
        return NotificationReads::query()->firstOrCreate(
            ['notification_id' => $notification->id, 'user_id' => $user->id],
            ['read_at' => now()]
        );
    }

    public function deleteWithReads(Notification $notification): void
    {
        $notification->reads()->delete();
        $notification->delete();
    }
}
