<?php

namespace App\Modules\Notifycation\Services;

use App\Models\Notification;
use App\Models\NotificationReads;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Thông báo hiển thị trong app: thông báo riêng của user + thông báo chung,
 * chỉ của website đang chạy (VI_TRI_WEB).
 */
class NotificationApiService
{
    public const PER_PAGE = 20;

    public function list(User $user, array $filters): LengthAwarePaginator
    {
        return $this->visibleQuery($user)
            ->when(($filters['read'] ?? null) !== null && $filters['read'] !== '', function (Builder $q) use ($user, $filters) {
                $isRead = filter_var($filters['read'], FILTER_VALIDATE_BOOLEAN);
                $isRead
                    ? $q->whereHas('reads', fn ($r) => $r->where('user_id', $user->id))
                    : $q->whereDoesntHave('reads', fn ($r) => $r->where('user_id', $user->id));
            })
            ->latest()
            ->latest('id')
            ->paginate(self::PER_PAGE)
            ->withQueryString();
    }

    public function unreadCount(User $user): int
    {
        return Notification::query()
            ->visibleTo($user)
            ->forCurrentSite()
            ->whereDoesntHave('reads', fn ($q) => $q->where('user_id', $user->id))
            ->count();
    }

    /**
     * Chi tiết thông báo; mở xem là tính đã đọc. Trả null nếu user không được xem.
     */
    public function detail(User $user, int $notificationId): ?Notification
    {
        $notification = $this->visibleQuery($user)->find($notificationId);

        if (!$notification) {
            return null;
        }

        if ($notification->reads->isEmpty()) {
            $this->markAsRead($user, $notification);
            $notification->load(['reads' => fn ($q) => $q->where('user_id', $user->id)]);
        }

        return $notification;
    }

    public function markAsRead(User $user, Notification $notification): NotificationReads
    {
        $read = NotificationReads::query()->firstOrCreate(
            ['notification_id' => $notification->id, 'user_id' => $user->id],
            ['read_at' => now()]
        );

        if (empty($read->read_at)) {
            $read->read_at = now();
            $read->save();
        }

        return $read;
    }

    private function visibleQuery(User $user): Builder
    {
        return Notification::query()
            ->visibleTo($user)
            ->forCurrentSite()
            ->with(['reads' => fn ($q) => $q->where('user_id', $user->id)]);
    }
}
