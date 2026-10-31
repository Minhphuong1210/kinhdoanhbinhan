<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\ApnsConfig;

class FirebaseNotificationService
{
    /** Âm thanh riêng cho thông báo tiền về (mua hàng, hoàn phí) */
    public const SOUND_TIEN_VE = 'tien_ve.caf';
    public const SOUND_DEFAULT = 'default';

    /** FCM cho phép tối đa 500 token mỗi lần multicast */
    private const MULTICAST_LIMIT = 500;

    protected $messaging;

    public function __construct(private readonly FcmTokenService $tokens)
    {
        $credentials = config('firebase.credentials');

        $factory = (new Factory)
            ->withServiceAccount($credentials);

        $this->messaging = $factory->createMessaging();
    }

    /**
     * Gửi thông báo tới các thiết bị của user ở đúng vị trí (bảng fcm_user).
     *
     * @param string|null $viTri FcmUser::VI_TRI_CONG_TY | FcmUser::VI_TRI_HOP_TAC_XA.
     *                           null: gửi kiểu cũ tới users.fcm.
     */
    public function sendToUser($user, string $title, string $body, array $data = [], ?string $viTri = null)
    {
        $tokens = $viTri
            ? $this->tokens->tokensFor($user, $viTri)
            : array_filter([$user->fcm]);

        if (empty($tokens)) {
            return false;
        }

        try {
            $report = $this->messaging->sendMulticast($this->buildMessage($title, $body, $data, self::SOUND_TIEN_VE), $tokens);

            if ($viTri) {
                $this->tokens->removeInvalid(array_merge($report->invalidTokens(), $report->unknownTokens()));
            }

            if ($report->hasFailures()) {
                Log::warning('Firebase notification partial failure', [
                    'user_id'  => $user->id,
                    'vi_tri'   => $viTri,
                    'failures' => $report->failures()->count(),
                    'total'    => count($tokens),
                ]);
            }

            return $report->successes()->count() > 0;
        } catch (\Throwable $e) {
            Log::error('Firebase notification error', [
                'user_id' => $user->id,
                'vi_tri'  => $viTri,
                'error'   => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Gửi 1 thông báo tới nhiều token (thông báo chung của admin), âm thanh mặc định.
     *
     * @return int số thiết bị gửi thành công
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = [], ?string $image = null): int
    {
        $message = $this->buildMessage($title, $body, $data, self::SOUND_DEFAULT, $image);
        $success = 0;

        foreach (array_chunk(array_values(array_unique($tokens)), self::MULTICAST_LIMIT) as $chunk) {
            try {
                $report = $this->messaging->sendMulticast($message, $chunk);

                $success += $report->successes()->count();
                $this->tokens->removeInvalid(array_merge($report->invalidTokens(), $report->unknownTokens()));
            } catch (\Throwable $e) {
                Log::error('Firebase broadcast error', [
                    'tokens' => count($chunk),
                    'error'  => $e->getMessage(),
                ]);
            }
        }

        return $success;
    }

    private function buildMessage(string $title, string $body, array $data, string $sound, ?string $image = null): CloudMessage
    {
        return CloudMessage::new()
            ->withNotification(
                Notification::create($title, $body, $image)
            )
            ->withData($this->formatData($data))
            ->withApnsConfig(
                ApnsConfig::fromArray([
                    'payload' => [
                        'aps' => [
                            'sound' => $sound,
                        ],
                    ],
                ])
            );
    }

    private function formatData(array $data)
    {
        $result = [];

        foreach ($data as $key => $value) {
            $result[$key] = is_string($value)
                ? $value
                : json_encode($value);
        }

        return $result;
    }
}
