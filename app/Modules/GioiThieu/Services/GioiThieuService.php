<?php

namespace App\Modules\GioiThieu\Services;

use App\Modules\GioiThieu\Repositories\GioiThieuRepository;
use App\Modules\Shared\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

class GioiThieuService extends BaseService
{
    public function __construct(GioiThieuRepository $repository)
    {
        parent::__construct($repository);
    }

    public function list(): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin();
    }

    /** Ảnh giới thiệu lưu ở disk shared, không qua CDN */
    protected function storeImage(UploadedFile $file): string
    {
        return $file->store('gioi-thieu', 'shared');
    }

    protected function prepare(array $data): array
    {
        // Ưu tiên vi_tri_text (dropdown có nghĩa) hơn vi_tri (text cũ)
        $text = $data['vi_tri_text'] ?? null;
        $data['vi_tri'] = $text !== null && $text !== '' ? $text : ($data['vi_tri'] ?? null);
        unset($data['vi_tri_text']);

        return $data;
    }
}
