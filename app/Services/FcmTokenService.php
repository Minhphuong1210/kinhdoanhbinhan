<?php

namespace App\Services;

use App\Models\FcmUser;
use App\Models\User;
use Carbon\Carbon;

/**
 * Lưu / xoá FCM token theo thiết bị và vị trí (cong_ty | hop_tac_xa).
 */
class FcmTokenService
{
    public function register(User $user, string $token, ?string $viTri = null, ?string $deviceName = null): FcmUser
    {
        $viTri = $viTri ?: FcmUser::defaultViTri();

        // updateOrCreate theo (fcm, vi_tri): máy đổi tài khoản thì token chuyển sang user mới
        return FcmUser::updateOrCreate(
            ['fcm' => $token, 'vi_tri' => $viTri],
            [
                'user_id'      => $user->id,
                'device_name'  => $deviceName ? mb_substr($deviceName, 0, 255) : null,
                'last_used_at' => Carbon::now(),
            ]
        );
    }

    /**
     * Đăng xuất: xoá token của thiết bị này (mọi vị trí, hoặc chỉ 1 vị trí).
     */
    public function remove(User $user, string $token, ?string $viTri = null): void
    {
        FcmUser::where('user_id', $user->id)
            ->where('fcm', $token)
            ->when($viTri, fn ($q) => $q->where('vi_tri', $viTri))
            ->delete();
    }

    /**
     * Đóng tài khoản: xoá token của mọi thiết bị.
     */
    public function removeAll(User $user): void
    {
        FcmUser::where('user_id', $user->id)->delete();
    }

    /**
     * @return string[]
     */
    public function tokensFor(User $user, string $viTri): array
    {
        return FcmUser::where('user_id', $user->id)
            ->where('vi_tri', $viTri)
            ->pluck('fcm')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Xoá các token Firebase báo không còn dùng được.
     */
    public function removeInvalid(array $tokens): void
    {
        if (!empty($tokens)) {
            FcmUser::whereIn('fcm', $tokens)->delete();
        }
    }
}
