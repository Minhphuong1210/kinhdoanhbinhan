<?php

namespace App\Modules\Core\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FcmUser;
use App\Models\User;
use App\Modules\Core\Services\AuthApiService;
use App\Services\FcmTokenService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthApiController extends Controller
{
    public function __construct(private readonly AuthApiService $authApiService)
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

        $payload = $this->authApiService->login($data, $this->tokenName($request, $data));
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

        $payload = $this->authApiService->register($data, $this->tokenName($request, $data));
        $message = $payload['message'];
        unset($payload['message']);

        return $this->success($payload, $message, 201);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $this->authApiService->forgotPassword($data['email']);

        return $this->success(null, 'Mat khau moi da duoc gui ve email cua ban.');
    }

    public function forgotPin(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $this->authApiService->forgotPin($data['email']);

        return $this->success(null, 'Ma PIN moi da duoc gui ve email cua ban.');
    }

    public function checkPin(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pin' => ['required', 'digits_between:4,6', 'regex:/^[0-9]+$/'],
        ]);

        return $this->success([
            'valid' => $this->authApiService->checkPin($request->user(), $data['pin']),
        ]);
    }

    public function updatePin(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_pin' => ['nullable', 'digits_between:4,6', 'regex:/^[0-9]+$/'],
            'pin' => ['required', 'digits_between:4,6', 'regex:/^[0-9]+$/', 'confirmed'],
        ]);

        $this->authApiService->updatePin($request->user(), $data);

        return $this->success(null, 'Cap nhat ma PIN thanh cong.');
    }

    public function profile(Request $request): JsonResponse
    {
        return $this->success([
            'user' => $this->authApiService->userPayload($request->user()),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email,' . $request->user()->id],
            'tel' => ['nullable', 'string', 'max:20', 'regex:/^(0|\+84)[0-9]{9}$/', 'unique:users,tel,' . $request->user()->id],
            'address' => ['nullable', 'string', 'max:500'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'ward_id' => ['nullable', 'exists:wards,id'],
            'cccd' => ['nullable', 'string', 'max:50'],
            'tong_ngan_hang_id' => ['nullable', 'exists:tong_ngan_hang,id'],
            'ngan_hang_id' => ['nullable', 'exists:tong_ngan_hang,id'],
            'ngan_hang_chi_nhanh_id' => ['nullable', 'exists:ngan_hangs,id'],
            'chi_nhanh_ngan_hang' => ['nullable'],
            'stk' => ['nullable', 'string', 'max:100', 'regex:/^[0-9]+$/'],
            'chu_tk' => ['nullable', 'string', 'max:255'],
            'bank_area_id' => ['nullable', 'exists:thanh_pho_banks,id'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('avatars', 'shared');
        }

        $user = $this->authApiService->updateProfile($request->user(), $data);

        return $this->success([
            'user' => $this->authApiService->userPayload($user),
        ], 'Cap nhat thong tin thanh cong.');
    }

    public function organizationChart(Request $request): JsonResponse
    {
        $data = $request->validate([
            'max_depth' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $maxDepth = (int) ($data['max_depth'] ?? 6);
        $user = $request->user();

        $users = User::query()
            ->select([
                'id',
                'name',
                'email',
                'tel',
                'parent_id',
                'invite_by',
                'image',
                'active',
                'vi_tien',
                'vi_tien_mua_hang',
                'ngay_tham_gia',
                'ngay_mua_don_cuoi',
                'so_thanh_vien_gt',
                'level',
            ])
            ->whereNotNull('parent_id')
            ->orderBy('ngay_tham_gia', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->groupBy('parent_id');

        $tree = $this->organizationNode($user, $users, 0, $maxDepth, true);

        return $this->success([
            'max_depth' => $maxDepth,
            'root' => $tree,
        ]);
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

    public function closeAccount(Request $request): JsonResponse
    {
        $this->authApiService->closeAccount($request->user());

        return $this->success(null, 'Tai khoan da duoc dong.');
    }

    private function tokenName(Request $request, array $data): string
    {
        return $data['device_name'] ?? $request->userAgent() ?? 'api-token';
    }

    private function success($data = null, string $message = 'Thanh cong', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => (object) [],
            'links' => (object) [],
        ], $status);
    }

    private function organizationNode(User $user, $groupedUsers, int $depth, int $maxDepth, bool $isRoot = false): array
    {
        $allChildren = $groupedUsers->get($user->id) ?? collect();
        $children = $depth >= $maxDepth ? collect() : $allChildren;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'tel' => $user->tel,
            'parent_id' => $user->parent_id,
            'invite_by' => $user->invite_by,
            'image' => $this->assetUrl($user->image),
            'active' => (int) $user->active,
            'level' => $user->level,
            'depth' => $depth,
            'vi_tien' => (float) ($user->vi_tien ?? 0),
            'vi_tien_mua_hang' => (float) ($user->vi_tien_mua_hang ?? 0),
            'so_thanh_vien_gt' => (int) ($user->so_thanh_vien_gt ?? 0),
            'direct_children_count' => $allChildren->count(),
            'ngay_tham_gia' => $this->dateTimeString($user->ngay_tham_gia),
            'ngay_mua_don_cuoi' => $this->dateTimeString($user->ngay_mua_don_cuoi),
            'days_since_last_order' => $this->daysSince($user->ngay_mua_don_cuoi),
            'display' => [
                'title' => number_format($user->vi_tien ?? 0, 0, ',', '.') . ' VND',
                'date' => $this->daysSince($user->ngay_mua_don_cuoi) . ' ngay',
                'class_name' => $isRoot ? 'level-root' : $this->organizationNodeClass($user),
            ],
            'children' => $children
                ->map(fn (User $child) => $this->organizationNode($child, $groupedUsers, $depth + 1, $maxDepth))
                ->values()
                ->all(),
        ];
    }

    private function organizationNodeClass(User $user): string
    {
        $count = (int) ($user->so_thanh_vien_gt ?? 0);

        if ($count >= 10) {
            return 'level-10';
        }

        if ($count >= 5) {
            return 'level-5';
        }

        if ($count >= 3) {
            return 'level-3';
        }

        return 'level-0';
    }

    private function daysSince($date): int
    {
        if (!$date) {
            return 0;
        }

        return (int) Carbon::parse($date)->diffInDays(now());
    }

    private function dateTimeString($date): ?string
    {
        if (!$date) {
            return null;
        }

        return Carbon::parse($date)->format('Y-m-d H:i:s');
    }

    private function assetUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return function_exists('cdn') ? cdn($path) : asset($path);
    }
}
