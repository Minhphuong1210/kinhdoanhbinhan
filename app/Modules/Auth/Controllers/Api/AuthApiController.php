<?php

namespace App\Modules\Auth\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FcmUser;
use App\Modules\Auth\Services\AuthService;
use App\Modules\Shared\Traits\ApiResponse;
use App\Services\FcmTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthApiController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly AuthService $authService)
    {
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
            'fcm' => ['nullable', 'string', 'max:255'],
            'vi_tri' => ['nullable', 'in:' . implode(',', FcmUser::VI_TRIS)],
        ]);

        $payload = $this->authService->apiLogin($data, $this->tokenName($request, $data));
        $message = $payload['message'];
        unset($payload['message']);

        return $this->success($payload, $message);
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'tel' => ['required', 'string', 'max:20', 'regex:/^(0|\+84)[0-9]{9}$/', 'unique:users,tel'],
            'password' => ['required', 'string', 'min:6', 'max:255', 'confirmed'],
            'invite_by' => ['required', 'string', 'max:255'],
            'pin' => ['nullable', 'digits_between:4,6', 'regex:/^[0-9]+$/'],
            'address' => ['nullable', 'string', 'max:500'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'ward_id' => ['nullable', 'exists:wards,id'],
            'cccd' => ['nullable', 'string', 'max:50'],
            'ngan_hang' => ['nullable', 'string', 'max:255'],
            'ngan_hang_id' => ['nullable', 'exists:tong_ngan_hang,id'],
            'tong_ngan_hang_id' => ['nullable', 'exists:tong_ngan_hang,id'],
            'chi_nhanh_ngan_hang' => ['nullable'],
            'ngan_hang_chi_nhanh_id' => ['nullable', 'exists:ngan_hangs,id'],
            'stk' => ['nullable', 'string', 'max:100', 'regex:/^[0-9]+$/'],
            'chu_tk' => ['nullable', 'string', 'max:255'],
            'bank_area_id' => ['nullable', 'exists:thanh_pho_banks,id'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $payload = $this->authService->apiRegister($data, $this->tokenName($request, $data));
        $message = $payload['message'];
        unset($payload['message']);

        return $this->success($payload, $message, 201);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);

        $this->authService->resetPassword($data['email']);

        return $this->success(null, 'Neu email ton tai, mat khau moi da duoc gui ve email cua ban.');
    }

    public function forgotPin(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);

        $this->authService->resetPin($data['email']);

        return $this->success(null, 'Neu email ton tai, ma PIN moi da duoc gui ve email cua ban.');
    }

    public function logout(Request $request, FcmTokenService $fcmTokens): JsonResponse
    {
        $data = $request->validate([
            'fcm' => ['nullable', 'string', 'max:255'],
            'vi_tri' => ['nullable', 'in:' . implode(',', FcmUser::VI_TRIS)],
        ]);

        // Máy đã đăng xuất thì không nhận thông báo nữa
        if (!empty($data['fcm'])) {
            $fcmTokens->remove($request->user(), $data['fcm'], $data['vi_tri'] ?? null);
        }

        $request->user()->currentAccessToken()?->delete();

        return $this->success(null, 'Dang xuat thanh cong.');
    }

    private function tokenName(Request $request, array $data): string
    {
        return $data['device_name'] ?? $request->userAgent() ?? 'api-token';
    }
}
