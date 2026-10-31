<?php

namespace App\Modules\Notifycation\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Modules\Notifycation\Services\BroadcastNotificationService;
use App\Modules\Notifycation\Services\NotificationApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    public function __construct(private readonly NotificationApiService $notificationService)
    {
    }

    /**
     * GET /api/notifications — 20 thông báo/trang, mới nhất trước
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'read' => ['nullable', 'in:0,1,true,false'],
        ]);

        $user = $request->user();
        $notifications = $this->notificationService->list($user, $request->only('read'));

        return response()->json([
            'success' => true,
            'message' => 'Thanh cong',
            'data' => collect($notifications->items())->map(fn (Notification $n) => $this->payload($n))->values(),
            'meta' => [
                'unread_count' => $this->notificationService->unreadCount($user),
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                    'last_page' => $notifications->lastPage(),
                    'from' => $notifications->firstItem(),
                    'to' => $notifications->lastItem(),
                ],
            ],
            'links' => [
                'first' => $notifications->url(1),
                'last' => $notifications->url($notifications->lastPage()),
                'prev' => $notifications->previousPageUrl(),
                'next' => $notifications->nextPageUrl(),
            ],
        ]);
    }

    /**
     * GET /api/notifications/{id} — chi tiết, tự đánh dấu đã đọc
     */
    public function show(Request $request, int $notification): JsonResponse
    {
        $user = $request->user();
        $item = $this->notificationService->detail($user, $notification);

        if (!$item) {
            return $this->notFound();
        }

        return response()->json([
            'success' => true,
            'message' => 'Thanh cong',
            'data' => $this->payload($item),
            'meta' => [
                'unread_count' => $this->notificationService->unreadCount($user),
            ],
            'links' => (object) [],
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Thanh cong',
            'data' => [
                'unread_count' => $this->notificationService->unreadCount($request->user()),
            ],
            'meta' => (object) [],
            'links' => (object) [],
        ]);
    }

    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        $user = $request->user();

        if (!$notification->isVisibleTo($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Ban khong co quyen truy cap thong bao nay.',
                'data' => null,
                'meta' => (object) [],
                'links' => (object) [],
            ], 403);
        }

        $read = $this->notificationService->markAsRead($user, $notification);

        return response()->json([
            'success' => true,
            'message' => 'Da danh dau da doc.',
            'data' => [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'read' => true,
                'read_at' => $read->read_at ? \Illuminate\Support\Carbon::parse($read->read_at)->toISOString() : null,
            ],
            'meta' => (object) [],
            'links' => (object) [],
        ]);
    }

    private function payload(Notification $notification): array
    {
        $read = $notification->reads->first();

        return [
            'id' => $notification->id,
            'title' => $notification->title,
            // Đoạn trích chữ thường (nội dung thông báo chung là HTML)
            'excerpt' => BroadcastNotificationService::plainText($notification->content, 150),
            'content' => $notification->content,
            'image' => $notification->image && !filter_var($notification->image, FILTER_VALIDATE_URL)
                ? cdn($notification->image)
                : $notification->image,
            'target_type' => $notification->target_type,
            'target_id' => $notification->target_id,
            'is_broadcast' => $notification->target_type === Notification::TARGET_ALL,
            'read' => $read !== null,
            'read_at' => $read?->read_at ? \Illuminate\Support\Carbon::parse($read->read_at)->toISOString() : null,
            'order_id' => $notification->order_id ?? null,
            'vi_tri' => $notification->vi_tri,
            'created_at' => $notification->created_at?->toISOString(),
            'updated_at' => $notification->updated_at?->toISOString(),
        ];
    }

    private function notFound(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Khong tim thay thong bao.',
            'data' => null,
            'meta' => (object) [],
            'links' => (object) [],
        ], 404);
    }
}
