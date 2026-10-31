<?php

namespace App\Modules\ThuVien\Services;

use App\Modules\ThuVien\Repositories\ThuVienRepository;
use App\Modules\Shared\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ThuVienService extends BaseService
{
    protected ?string $uploadFolder = 'thu_vien';

    public function __construct(ThuVienRepository $repository)
    {
        parent::__construct($repository);
    }

    public function list(?string $search): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin($search);
    }

    protected function prepare(array $data): array
    {
        $data['slug'] = Str::slug($data['name']);
        $data['status'] = $this->flag($data, 'status');
        $data['featured'] = $this->flag($data, 'featured');

        return $data;
    }
}
