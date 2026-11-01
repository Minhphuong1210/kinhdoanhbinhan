<?php

namespace App\Modules\Media\Services;

use App\Modules\Media\Exceptions\MediaException;
use App\Modules\Media\Repositories\MediaRepository;
use Illuminate\Http\UploadedFile;

/**
 * Thêm, lấy link, xoá ảnh. Code ngoài module chỉ gọi service này hoặc helper cdn*().
 */
class MediaService
{
    public function __construct(private readonly MediaRepository $media)
    {
    }

    /**
     * @return string đường dẫn lưu trong DB, ví dụ products/abc.jpg
     *
     * @throws MediaException
     */
    public function upload(UploadedFile $file, string $folder = ''): string
    {
        $path = $this->media->store($file, $folder);

        if (!$path) {
            throw new MediaException('Không lưu được ảnh.');
        }

        return $path;
    }

    public function url(?string $path): string
    {
        return $path ? $this->media->url($path) : '';
    }

    /** Nhận đường dẫn hoặc link đầy đủ */
    public function delete(?string $path): bool
    {
        $path = $this->toRelativePath($path);

        if ($path === '') {
            return false;
        }

        try {
            return $this->media->exists($path) && $this->media->delete($path);
        } catch (\Exception) {
            return false;
        }
    }

    /** Ảnh dạng data URI, dùng khi xuất PDF */
    public function base64(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        try {
            if (!$this->media->exists($path)) {
                return null;
            }

            return 'data:' . $this->media->mimeType($path) . ';base64,' . base64_encode((string) $this->media->get($path));
        } catch (\Exception) {
            return null;
        }
    }

    private function toRelativePath(?string $path): string
    {
        $path = (string) $path;

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $path = rawurldecode((string) parse_url($path, PHP_URL_PATH));
            // Link CDN cũ có tiền tố /shared/
            $path = preg_replace('#^/shared/#', '', $path);
        }

        return ltrim($path, '/');
    }
}
