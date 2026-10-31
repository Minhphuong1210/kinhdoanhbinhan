<?php

namespace App\Modules\Shared\Services;

use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

/**
 * Thêm / sửa / xoá dùng chung. Module cần xử lý riêng thì override prepare().
 */
abstract class BaseService
{
    /** Thư mục CDN của ảnh upload, để null nếu module không có ảnh */
    protected ?string $uploadFolder = null;

    public function __construct(protected BaseRepository $repository)
    {
    }

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function create(array $data): Model
    {
        return $this->repository->create($this->prepare($this->handleImage($data)));
    }

    public function update(Model $model, array $data): Model
    {
        return $this->repository->update($model, $this->prepare($this->handleImage($data)));
    }

    public function delete(Model $model): void
    {
        $this->repository->delete($model);
    }

    protected function prepare(array $data): array
    {
        return $data;
    }

    /** Upload ảnh mới, không gửi ảnh thì giữ ảnh cũ */
    protected function handleImage(array $data): array
    {
        if (($data['image'] ?? null) instanceof UploadedFile) {
            $data['image'] = $this->storeImage($data['image']);
        } else {
            unset($data['image']);
        }

        return $data;
    }

    protected function storeImage(UploadedFile $file): string
    {
        return cdn_upload($file, $this->uploadFolder);
    }

    /** Xoá các ảnh trên CDN được chèn trong nội dung CKEditor */
    protected function deleteEditorImages(?string $html): void
    {
        if (empty($html)) {
            return;
        }

        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $html, $matches);

        foreach ($matches[1] as $url) {
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                cdn_delete($url);
            }
        }
    }

    /** Checkbox: thiếu hoặc rỗng là false */
    protected function flag(array $data, string $key): bool
    {
        return filter_var($data[$key] ?? false, FILTER_VALIDATE_BOOLEAN);
    }
}
