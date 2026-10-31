<?php

namespace App\Modules\Category\Repositories;

use App\Modules\Category\Models\Category;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryRepository extends BaseRepository
{
    protected string $model = Category::class;

    public function paginateForAdmin(?string $search): LengthAwarePaginator
    {
        $query = $this->query()->with('parent')->whereNull('parent_id')->latest();

        return $this->paginateWithSearch($query, $search, 'name');
    }

    public function rootCategories(?int $exceptId = null)
    {
        return $this->query()
            ->whereNull('parent_id')
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->get();
    }
}
