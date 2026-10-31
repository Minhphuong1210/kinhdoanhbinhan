<?php
namespace App\Modules\Core\Services;

use App\Models\NganHangBank;
use App\Models\Role;
use App\Models\TongNganHang;
use App\Models\User;
use App\Services\FcmTokenService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthApiService
{
    public function __construct(private readonly FcmTokenService $fcmTokens)
    {
    }

    public function login(array $data, string $tokenName): array
    {
        $login = trim($data['login']);
        $user  = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? User::where('email', $login)->first()
            : User::where('tel', $login)->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['Thong tin dang nhap khong dung.'],
            ]);
        }

        if (! $user->active) {
            throw ValidationException::withMessages([
                'login' => ['Tai khoan da bi khoa. Vui long lien he admin.'],
            ]);
        }
        $user->update(['fcm' => $data['fcm'] ?? null]);

        if (!empty($data['fcm'])) {
            $this->fcmTokens->register($user, $data['fcm'], $data['vi_tri'] ?? null, $data['device_name'] ?? null);
        }

        return $this->tokenPayload($user, $tokenName, 'Dang nhap thanh cong.');
    }

    public function register(array $data, string $tokenName): array
    {
        $parent = User::where('id', $data['invite_by'])
            ->orWhere('email', $data['invite_by'])
            ->orWhere('tel', $data['invite_by'])
            ->first();

        if (! $parent) {
            throw ValidationException::withMessages([
                'invite_by' => ['Ma gioi thieu khong ton tai.'],
            ]);
        }

        $bankData = $this->resolveBankData($data);

        $user = User::create([
            'name'                => $data['name'],
            'email'               => $data['email'],
            'tel'                 => $data['tel'],
            'password'            => Hash::make($data['password']),
            'province_id'         => $data['province_id'] ?? null,
            'district_id'         => $data['district_id'] ?? null,
            'ward_id'             => $data['ward_id'] ?? null,
            'address'             => $data['address'] ?? null,
            'cccd'                => $data['cccd'] ?? null,
            'ngan_hang'           => $bankData['ngan_hang'] ?? null,
            'ngan_hang_id'        => $bankData['ngan_hang_id'] ?? null,
            'chi_nhanh_ngan_hang' => $bankData['chi_nhanh_ngan_hang'] ?? null,
            'stk'                 => $data['stk'] ?? null,
            'chu_tk'              => $data['chu_tk'] ?? null,
            'pin'                 => $data['pin'] ?? null,
            'invite_by'           => $parent->id,
            'active'              => 1,
            'vi_tien'             => 0,
            'bank_area_id'        => $data['bank_area_id'] ?? null,
        ]);

        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            $user->roles()->attach($userRole->id, ['admin_id' => $user->id, 'role_id' => $userRole->id]);
        }

        return $this->tokenPayload($user, $tokenName, 'Dang ky thanh cong.');
    }

    public function updateProfile(User $user, array $data): User
    {
        $bankData = $this->resolveBankData($data);

        $updateData = array_filter([
            'name'         => $data['name'] ?? null,
            'email'        => $data['email'] ?? null,
            'tel'          => $data['tel'] ?? null,
            'province_id'  => $data['province_id'] ?? null,
            'district_id'  => $data['district_id'] ?? null,
            'ward_id'      => $data['ward_id'] ?? null,
            'address'      => $data['address'] ?? null,
            'cccd'         => $data['cccd'] ?? null,
            'stk'          => $data['stk'] ?? null,
            'chu_tk'       => $data['chu_tk'] ?? null,
            'bank_area_id' => $data['bank_area_id'] ?? null,
            'image'        => $data['image'] ?? null,
        ], fn($value) => $value !== null);

        $user->update(array_merge($updateData, $bankData));

        return $user->fresh();
    }

    private function resolveBankData(array $data): array
    {
        $bankId   = $data['tong_ngan_hang_id'] ?? $data['ngan_hang_id'] ?? null;
        $branchId = $data['ngan_hang_chi_nhanh_id'] ?? $data['chi_nhanh_ngan_hang'] ?? null;
        $bank     = $bankId ? TongNganHang::find($bankId) : null;
        $branch   = $branchId ? NganHangBank::find($branchId) : null;

        if ($branch && ! empty($data['bank_area_id']) && (int) $branch->thanh_pho_id !== (int) $data['bank_area_id']) {
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
        ], fn($value) => $value !== null);
    }

    public function forgotPassword(string $email): void
    {
        $user     = User::where('email', $email)->firstOrFail();
        $password = Str::random(10);

        $user->forceFill(['password' => Hash::make($password)])->save();

        Mail::send('Core::Admin.emails.reset-password', [
            'user'     => $user,
            'password' => $password,
        ], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                ->subject('Mat khau moi cua ban');
        });
    }

    public function forgotPin(string $email): void
    {
        $user = User::where('email', $email)->firstOrFail();
        $pin  = (string) random_int(100000, 999999);

        $user->forceFill(['pin' => $pin])->save();

        Mail::send('Core::Admin.emails.reset-pin', [
            'user' => $user,
            'pin'  => $pin,
        ], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                ->subject('Ma PIN moi cua ban');
        });
    }

    public function checkPin(User $user, string $pin): bool
    {
        return hash_equals((string) $user->pin, (string) $pin);
    }

    public function updatePin(User $user, array $data): void
    {
        if ($user->pin && ! hash_equals((string) $user->pin, (string) ($data['current_pin'] ?? ''))) {
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

    public function userPayload(User $user): array
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
            'image'               => $this->assetUrl($user->image),
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

    private function tokenPayload(User $user, string $tokenName, string $message): array
    {
        return [
            'message'      => $message,
            'token_type'   => 'Bearer',
            'access_token' => $user->createToken($tokenName)->plainTextToken,
            'user'         => $this->userPayload($user),
        ];
    }

    private function assetUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return function_exists('cdn') ? cdn($path) : asset($path);
    }
}
