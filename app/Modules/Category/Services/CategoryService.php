<?php

namespace App\Modules\Category\Services;

use App\Modules\Category\Repositories\CategoryRepository;
use App\Modules\Shared\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class CategoryService extends BaseService
{
    protected ?string $uploadFolder = 'categories';

    public function __construct(CategoryRepository $repository)
    {
        parent::__construct($repository);
    }

    public function list(?string $search): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin($search);
    }

    public function rootCategories(?int $exceptId = null)
    {
        return $this->repository->rootCategories($exceptId);
    }

    protected function prepare(array $data): array
    {
        $data['slug'] = Str::slug($data['name']);
        $data['active'] = $this->flag($data, 'active');

        return $data;
    }
}
