<?php

namespace App\Modules\User\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Shared\Traits\ApiResponse;
use App\Modules\User\Requests\UpdateProfileApiRequest;
use App\Modules\User\Services\MemberTreeService;
use App\Modules\User\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileApiController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ProfileService $profileService,
        private readonly MemberTreeService $treeService
    ) {
    }

    public function profile(Request $request): JsonResponse
    {
        return $this->success([
            'user' => $this->profileService->payload($request->user()),
        ]);
    }

    public function updateProfile(UpdateProfileApiRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('avatars', 'shared');
        }

        $user = $this->profileService->updateProfile($request->user(), $data);

        return $this->success([
            'user' => $this->profileService->payload($user),
        ], 'Cap nhat thong tin thanh cong.');
    }

    public function organizationChart(Request $request): JsonResponse
    {
        $data = $request->validate([
            'max_depth' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $maxDepth = (int) ($data['max_depth'] ?? 6);

        return $this->success([
            'max_depth' => $maxDepth,
            'root' => $this->treeService->organizationChart($request->user(), $maxDepth),
        ]);
    }

    public function checkPin(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pin' => ['required', 'digits_between:4,6', 'regex:/^[0-9]+$/'],
        ]);

        return $this->success([
            'valid' => $this->profileService->checkPin($request->user(), $data['pin']),
        ]);
    }

    public function updatePin(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_pin' => ['nullable', 'digits_between:4,6', 'regex:/^[0-9]+$/'],
            'pin' => ['required', 'digits_between:4,6', 'regex:/^[0-9]+$/', 'confirmed'],
        ]);

        $this->profileService->updatePin($request->user(), $data);

        return $this->success(null, 'Cap nhat ma PIN thanh cong.');
    }

    public function closeAccount(Request $request): JsonResponse
    {
        $this->profileService->closeAccount($request->user());

        return $this->success(null, 'Tai khoan da duoc dong.');
    }
}
