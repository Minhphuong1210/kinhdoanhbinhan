<?php

namespace App\Modules\Media\Repositories;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Nơi duy nhất làm việc trực tiếp với kho lưu ảnh (R2 hoặc thư mục local).
 */
class MediaRepository
{
    public const DISK = 'media';

    public function store(UploadedFile $file, string $folder): string|false
    {
        return $this->disk()->putFile($folder, $file);
    }

    public function exists(string $path): bool
    {
        return $this->disk()->exists($path);
    }

    public function delete(string $path): bool
    {
        return $this->disk()->delete($path);
    }

    public function get(string $path): ?string
    {
        return $this->disk()->get($path);
    }

    public function mimeType(string $path): string|false
    {
        return $this->disk()->mimeType($path);
    }

    public function url(string $path): string
    {
        $base = rtrim((string) config('media.r2.public_url'), '/');

        return $base === '' ? '' : $base . '/' . ltrim($path, '/');
    }

    /** @return string[] */
    public function allFiles(): array
    {
        return $this->disk()->allFiles();
    }

    private function disk(): Filesystem
    {
        return Storage::disk(self::DISK);
    }
}
