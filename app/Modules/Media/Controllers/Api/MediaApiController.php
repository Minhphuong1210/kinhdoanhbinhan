<?php

namespace App\Modules\Media\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Media\Exceptions\MediaException;
use App\Modules\Media\Requests\MediaUrlRequest;
use App\Modules\Media\Requests\UploadMediaRequest;
use App\Modules\Media\Resources\MediaResource;
use App\Modules\Media\Services\MediaService;
use Illuminate\Http\JsonResponse;

class MediaApiController extends Controller
{
    public function __construct(private readonly MediaService $mediaService)
    {
    }

    /**
     * POST /api/media — thêm ảnh, trả về path để lưu DB và url để hiển thị
     */
    public function store(UploadMediaRequest $request): JsonResponse
    {
        $folder = $request->input('folder', config('media.upload.default_folder'));

        try {
            $path = $this->mediaService->upload($request->file('image'), $folder);
        } catch (MediaException $e) {
            report($e);

            return response()->json(['success' => false, 'message' => 'Khong luu duoc anh.', 'data' => null], 500);
        }

        return $this->respond($path, 'Tai anh thanh cong', 201);
    }

    /**
     * GET /api/media/url?path=products/abc.jpg — lấy link hiển thị từ path trong DB
     */
    public function url(MediaUrlRequest $request): JsonResponse
    {
        return $this->respond($request->validated('path'), 'Thanh cong');
    }

    private function respond(string $path, string $message, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => new MediaResource($path),
        ], $status);
    }
}
