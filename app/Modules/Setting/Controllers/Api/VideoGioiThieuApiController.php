<?php

namespace App\Modules\Setting\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Setting\Repositories\SettingRepository;
use Illuminate\Http\JsonResponse;

class VideoGioiThieuApiController extends Controller
{
    public function __construct(private readonly SettingRepository $settings)
    {
    }

    /**
     * GET /api/video-gioi-thieu
     */
    public function video(): JsonResponse
    {
        return $this->videoResponse('video_kinhdoanhbinhan', 'Chưa có video giới thiệu.', 'Lấy video giới thiệu thành công.');
    }

    /**
     * GET /api/video-gioi-thieu-ve-chung-toi — admin upload ở Cài đặt (theo website đang chạy)
     */
    public function gioiThieuVeChungToi(): JsonResponse
    {
        return $this->videoResponse(
            'video_gioi_thieu_ve_chung_toi',
            'Chưa có video giới thiệu về chúng tôi.',
            'Lấy video giới thiệu về chúng tôi thành công.'
        );
    }

    private function videoResponse(string $name, string $emptyMessage, string $successMessage): JsonResponse
    {
        $value = $this->settings->valueOf($name);

        if (!$value) {
            return response()->json([
                'success' => false,
                'message' => $emptyMessage,
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => $successMessage,
            'data' => [
                'name' => $name,
                'video' => cdn($value),
            ],
        ]);
    }
}
