<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\Permission\Models\Role;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\User\Services\ProfileService;
use App\Services\FcmTokenService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Đăng nhập, đăng ký, quên mật khẩu / PIN cho cả web và API.
 */
class AuthService
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly ProfileService $profile,
        private readonly FcmTokenService $fcmTokens
    ) {
    }

    /** Trả về user nếu đúng tài khoản + mật khẩu */
    public function findByCredentials(string $login, string $password): ?User
    {
        $user = $this->users->findByLogin(trim($login));

        if (!$user) {
            return null;
        }

        try {
            return Hash::check($password, $user->password) ? $user : null;
        } catch (\RuntimeException) {
            // Mật khẩu cũ không phải bcrypt
            return null;
        }
    }

    public function findReferrer(?string $code): ?User
    {
        return $code ? $this->users->findByReferralCode($code) : null;
    }

    /** Tạo tài khoản mới và gán role 'user' mặc định */
    public function createAccount(array $attributes, User $referrer): User
    {
        $user = $this->users->create($attributes + [
            'invite_by' => $referrer->id,
            'active'    => 1,
            'vi_tien'   => 0,
        ]);

        $userRole = Role::where('name', 'user')->first();

        if ($userRole) {
            $user->roles()->attach($userRole->id, ['admin_id' => $user->id, 'role_id' => $userRole->id]);
        }

        return $user;
    }

    public function apiLogin(array $data, string $tokenName): array
    {
        $user = $this->findByCredentials($data['login'], $data['password']);

        if (!$user) {
            throw ValidationException::withMessages([
                'login' => ['Thong tin dang nhap khong dung.'],
            ]);
        }

        if (!$user->active) {
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

    public function apiRegister(array $data, string $tokenName): array
    {
        $referrer = $this->findReferrer($data['invite_by']);

        if (!$referrer) {
            throw ValidationException::withMessages([
                'invite_by' => ['Ma gioi thieu khong ton tai.'],
            ]);
        }

        $bankData = $this->profile->resolveBankData($data);

        $user = $this->createAccount([
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
            'bank_area_id'        => $data['bank_area_id'] ?? null,
        ], $referrer);

        return $this->tokenPayload($user, $tokenName, 'Dang ky thanh cong.');
    }

    public function resetPassword(string $email): void
    {
        $user = $this->users->findByEmail($email);

        if (!$user) {
            return;
        }

        $password = Str::random(10);
        $user->forceFill(['password' => Hash::make($password)])->save();

        Mail::send('Core::Admin.emails.reset-password', compact('user', 'password'), function ($message) use ($user) {
            $message->to($user->email, $user->name)->subject('Mật khẩu mới của bạn');
        });
    }

    public function resetPin(string $email): void
    {
        $user = $this->users->findByEmail($email);

        if (!$user) {
            return;
        }

        $pin = (string) random_int(100000, 999999);
        $user->forceFill(['pin' => $pin])->save();

        Mail::send('Core::Admin.emails.reset-pin', compact('user', 'pin'), function ($message) use ($user) {
            $message->to($user->email, $user->name)->subject('Mã PIN mới của bạn');
        });
    }

    private function tokenPayload(User $user, string $tokenName, string $message): array
    {
        return [
            'message'      => $message,
            'token_type'   => 'Bearer',
            'access_token' => $user->createToken($tokenName)->plainTextToken,
            'user'         => $this->profile->payload($user),
        ];
    }
}
