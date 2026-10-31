<?php

namespace App\Modules\Notifycation\Services;

use App\Models\FcmUser;
use App\Models\Notification;
use App\Modules\Notifycation\Jobs\SendBroadcastPushJob;
use App\Modules\Notifycation\Repositories\NotificationRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

/**
 * Thông báo admin gửi cho tất cả mọi người ở app công ty, app HTX hoặc cả 2.
 */
class BroadcastNotificationService
{
    public const TARGETS = [
        FcmUser::VI_TRI_CONG_TY       => 'App công ty',
        FcmUser::VI_TRI_HOP_TAC_XA    => 'App hợp tác xã',
        Notification::VI_TRI_TAT_CA   => 'Cả 2 app',
    ];

    public function __construct(private readonly NotificationRepository $notifications)
    {
    }

    public function list(array $filters): LengthAwarePaginator
    {
        return $this->notifications->paginateBroadcasts($filters);
    }

    public function send(string $title, string $content, string $viTri, ?UploadedFile $image = null): Notification
    {
        $notification = $this->notifications->create([
            'target_type' => Notification::TARGET_ALL,
            'target_id'   => 0,
            'title'       => $title,
            'content'     => $content,
            'vi_tri'      => $viTri,
            'image'       => $image ? cdn_upload($image, 'notifications') : null,
        ]);

        // QUEUE_CONNECTION=sync thì chạy ngay; có queue worker thì gửi nền
        SendBroadcastPushJob::dispatch($notification->id);

        return $notification;
    }

    /**
     * Sửa nội dung hiển thị trong app. Push đã gửi tới điện thoại thì không gửi lại.
     */
    public function update(Notification $notification, string $title, string $content, string $viTri, ?UploadedFile $image = null): Notification
    {
        $data = [
            'title'   => $title,
            'content' => $content,
            'vi_tri'  => $viTri,
        ];

        if ($image) {
            $data['image'] = cdn_upload($image, 'notifications');
        }

        $notification->update($data);

        return $notification;
    }

    /**
     * Ảnh chèn trong nội dung CKEditor.
     */
    public function uploadEditorImage(UploadedFile $file): string
    {
        return cdn(cdn_upload($file, 'notifications/content'));
    }

    /**
     * Nội dung CKEditor (HTML) → chữ thường, dùng cho push và đoạn trích ở danh sách.
     */
    public static function plainText(?string $html, int $limit = 200): string
    {
        $text = html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], ' ', (string) $html)), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/u', ' ', $text)), $limit);
    }

    public function delete(Notification $notification): void
    {
        $this->notifications->deleteWithReads($notification);
    }

    /**
     * vi_tri của fcm_user nhận thông báo này.
     *
     * @return string[]
     */
    public static function fcmViTris(string $viTri): array
    {
        return $viTri === Notification::VI_TRI_TAT_CA ? FcmUser::VI_TRIS : [$viTri];
    }
}
