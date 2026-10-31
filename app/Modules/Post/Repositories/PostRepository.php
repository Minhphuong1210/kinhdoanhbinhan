<?php

namespace App\Modules\Post\Repositories;

use App\Modules\Post\Models\Post;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostRepository extends BaseRepository
{
    protected string $model = Post::class;

    public function paginateForAdmin(?string $search, mixed $categoryId): LengthAwarePaginator
    {
        $query = $this->query()
            ->with('category')
            ->when($categoryId, fn ($q) => $q->where('category_post_id', $categoryId))
            ->latest();

        return $this->paginateWithSearch($query, $search, 'name');
    }
}
