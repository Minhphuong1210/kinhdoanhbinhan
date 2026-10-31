<?php

namespace App\Modules\CategoryPost\Repositories;

use App\Modules\CategoryPost\Models\CategoryPost;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryPostRepository extends BaseRepository
{
    protected string $model = CategoryPost::class;

    public function paginateForAdmin(?string $search): LengthAwarePaginator
    {
        return $this->paginateWithSearch($this->query()->withCount('posts')->latest(), $search, 'name');
    }
}
