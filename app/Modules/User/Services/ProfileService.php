<?php

namespace App\Modules\User\Services;

use App\Models\NganHangBank;
use App\Models\TongNganHang;
use App\Models\User;
use App\Services\FcmTokenService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Thông tin cá nhân, mã PIN và đóng tài khoản của chính user.
 */
class ProfileService
{
    public function __construct(private readonly FcmTokenService $fcmTokens)
    {
    }

    /** Web: cập nhật địa chỉ (middleware profile.complete chuyển về đây) */
    public function updateAddress(User $user, array $data): void
    {
        $update = Arr::only($data, ['province_id', 'district_id', 'ward_id', 'address']);
        $update['chi_nhanh_ngan_hang'] = $data['chi_nhanh_ngan_hang'] ?? '';

        $user->update($update + $this->credentialChanges($data));
    }

    /** Web: trang thông tin cá nhân */
    public function updateInfo(User $user, array $data): void
    {
        $update = Arr::only($data, [
            'name', 'tel', 'province_id', 'district_id', 'ward_id',
            'address', 'ngan_hang', 'stk', 'cccd', 'chu_tk', 'bank_area_id',
        ]);

        if (!empty($data['email'])) {
            $update['email'] = $data['email'];
        }

        $update['ngan_hang_id'] = $data['tong_ngan_hang_id'] ?? null;
        $update['chi_nhanh_ngan_hang'] = $data['ngan_hang_chi_nhanh_id'] ?? null;

        if (!empty($data['image'])) {
            $update['image'] = cdn_upload($data['image'], 'avatars');
        }

        $user->update($update + $this->credentialChanges($data));
    }

    /** API: chỉ cập nhật trường được gửi lên */
    public function updateProfile(User $user, array $data): User
    {
        $bankData = $this->resolveBankData($data);

        $updateData = array_filter(
            Arr::only($data, [
                'name', 'email', 'tel', 'province_id', 'district_id', 'ward_id',
                'address', 'cccd', 'stk', 'chu_tk', 'bank_area_id', 'image',
            ]),
            fn ($value) => $value !== null
        );

        $user->update(array_merge($updateData, $bankData));

        return $user->fresh();
    }

    public function resolveBankData(array $data): array
    {
        $bankId   = $data['tong_ngan_hang_id'] ?? $data['ngan_hang_id'] ?? null;
        $branchId = $data['ngan_hang_chi_nhanh_id'] ?? $data['chi_nhanh_ngan_hang'] ?? null;
        $bank     = $bankId ? TongNganHang::find($bankId) : null;
        $branch   = $branchId ? NganHangBank::find($branchId) : null;

        if ($branch && !empty($data['bank_area_id']) && (int) $branch->thanh_pho_id !== (int) $data['bank_area_id']) {
            throw ValidationException::withMessages([
                'ngan_hang_chi_nhanh_id' => ['Chi nhanh ngan hang khong thuoc khu vuc da chon.'],
            ]);
        }

        if ($branch && $bankId && $branch->tong_ngan_hang_id && (int) $branch->tong_ngan_hang_id !== (int) $bankId) {
            throw ValidationException::withMessages([
                'ngan_hang_chi_nhanh_id' => ['Chi nhanh ngan hang khong thuoc ngan hang da chon.'],
            ]);
        }

        return array_filter([
            'ngan_hang'           => $bank?->name ?? $data['ngan_hang'] ?? null,
            'ngan_hang_id'        => $bankId,
            'chi_nhanh_ngan_hang' => $branch?->id ?? $branchId,
        ], fn ($value) => $value !== null);
    }

    public function checkPin(User $user, string $pin): bool
    {
        return hash_equals((string) $user->pin, $pin);
    }

    public function updatePin(User $user, array $data): void
    {
        if ($user->pin && !hash_equals((string) $user->pin, (string) ($data['current_pin'] ?? ''))) {
            throw ValidationException::withMessages([
                'current_pin' => ['Ma PIN hien tai khong dung.'],
            ]);
        }

        $user->forceFill(['pin' => $data['pin']])->save();
    }

    /**
     * Người dùng tự đóng tài khoản: chỉ chuyển active = 0 (admin có thể bật lại),
     * thu hồi mọi token đăng nhập và FCM để không nhận thông báo nữa.
     */
    public function closeAccount(User $user): void
    {
        $user->forceFill(['active' => 0, 'fcm' => null])->save();
        $user->tokens()->delete();
        $this->fcmTokens->removeAll($user);
    }

    public function payload(User $user): array
    {
        return [
            'id'                  => $user->id,
            'name'                => $user->name,
            'email'               => $user->email,
            'tel'                 => $user->tel,
            'address'             => $user->address,
            'province_id'         => $user->province_id,
            'district_id'         => $user->district_id,
            'ward_id'             => $user->ward_id,
            'cccd'                => $user->cccd,
            'active'              => (int) $user->active,
            'image'               => asset_url($user->image),
            'vi_tien'             => $user->vi_tien,
            'vi_tien_mua_hang'    => $user->vi_tien_mua_hang,
            'invite_by'           => $user->invite_by,
            'bank_area_id'        => $user->bank_area_id,
            'ngan_hang'           => $user->ngan_hang,
            'ngan_hang_id'        => $user->ngan_hang_id,
            'chi_nhanh_ngan_hang' => $user->chi_nhanh_ngan_hang,
            'stk'                 => $user->stk,
            'chu_tk'              => $user->chu_tk,
        ];
    }

    private function credentialChanges(array $data): array
    {
        $changes = [];

        if (!empty($data['password'])) {
            $changes['password'] = Hash::make($data['password']);
        }

        if (!empty($data['pin'])) {
            $changes['pin'] = $data['pin'];
        }

        return $changes;
    }
}
