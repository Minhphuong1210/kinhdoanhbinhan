<?php

namespace App\Modules\Notifycation\Jobs;

use App\Models\FcmUser;
use App\Models\Notification;
use App\Modules\Notifycation\Services\BroadcastNotificationService;
use App\Services\FirebaseNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Đẩy FCM thông báo chung tới mọi thiết bị thuộc vị trí đã chọn.
 */
class SendBroadcastPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;

    public function __construct(public readonly int $notificationId)
    {
    }

    public function handle(FirebaseNotificationService $firebase): void
    {
        $notification = Notification::find($this->notificationId);

        if (!$notification) {
            return;
        }

        $data = [
            'type'            => 'broadcast',
            'notification_id' => $notification->id,
        ];
        $image = $notification->image ? cdn($notification->image) : null;

        $sent = 0;

        FcmUser::whereIn('vi_tri', BroadcastNotificationService::fcmViTris($notification->vi_tri))
            ->select(['id', 'fcm'])
            ->chunkById(500, function ($rows) use ($firebase, $notification, $data, $image, &$sent) {
                $sent += $firebase->sendToTokens(
                    $rows->pluck('fcm')->all(),
                    $notification->title,
                    // Nội dung là HTML (CKEditor) → push chỉ gửi chữ
                    BroadcastNotificationService::plainText($notification->content),
                    $data,
                    $image
                );
            });

        Log::info('[Broadcast] Đã gửi thông báo chung', [
            'notification_id' => $notification->id,
            'vi_tri'          => $notification->vi_tri,
            'sent'            => $sent,
        ]);
    }
}
