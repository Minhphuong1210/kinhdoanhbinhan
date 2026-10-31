<?php

namespace App\Modules\Post\Services;

use App\Modules\Post\Repositories\PostRepository;
use App\Modules\Shared\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class PostService extends BaseService
{
    protected ?string $uploadFolder = 'posts';

    public function __construct(PostRepository $repository)
    {
        parent::__construct($repository);
    }

    public function list(?string $search, mixed $categoryId): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin($search, $categoryId);
    }

    public function uploadContentImage(UploadedFile $file): string
    {
        return cdn_upload($file, 'posts/content');
    }

    /** Xoá bài viết cùng ảnh đại diện và ảnh trong nội dung */
    public function delete(Model $model): void
    {
        if ($model->image) {
            cdn_delete($model->image);
        }

        $this->deleteEditorImages($model->content);
        $this->deleteEditorImages($model->description);

        parent::delete($model);
    }

    protected function prepare(array $data): array
    {
        $data['slug'] = Str::slug($data['name']);
        $data['status'] = $data['status'] ?? 'draft';
        $data['featured'] = $this->flag($data, 'featured');

        return $data;
    }
}
