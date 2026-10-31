<?php

namespace App\Modules\CategoryPost\Services;

use App\Modules\CategoryPost\Repositories\CategoryPostRepository;
use App\Modules\Shared\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class CategoryPostService extends BaseService
{
    public function __construct(CategoryPostRepository $repository)
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
        $data['active'] = $this->flag($data, 'active');

        return $data;
    }
}
