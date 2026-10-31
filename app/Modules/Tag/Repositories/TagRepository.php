<?php

namespace App\Modules\Tag\Repositories;

use App\Modules\Shared\Repositories\BaseRepository;
use App\Modules\Tag\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TagRepository extends BaseRepository
{
    protected string $model = Tag::class;

    public function paginateWithProductCount(?string $search, int $perPage = 20): LengthAwarePaginator
    {
        return $this->query()
            ->withCount('products')
            ->when($search, fn ($q) => $q->where('name', 'like', "%$search%"))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}
