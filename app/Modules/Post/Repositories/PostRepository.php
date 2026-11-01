<?php

namespace App\Modules\Post\Repositories;

use App\Modules\Post\Models\Post;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PostRepository extends BaseRepository
{
    protected string $model = Post::class;

    /** Bài đã đăng của website đang chạy */
    public function published(): Builder
    {
        return $this->query()->forWeb()->where('status', 'published');
    }

    public function paginatePublished(?int $categoryId, string $keyword): LengthAwarePaginator
    {
        return $this->published()
            ->with('category')
            ->when($categoryId !== null, fn ($q) => $q->where('category_post_id', $categoryId))
            ->when($keyword !== '', fn ($q) => $q->where('name', 'like', "%{$keyword}%"))
            ->latest()
            ->paginate(12)
            ->withQueryString();
    }

    public function relatedTo(Post $post, int $limit): Collection
    {
        return $this->published()
            ->where('category_post_id', $post->category_post_id)
            ->where('id', '!=', $post->id)
            ->latest()
            ->take($limit)
            ->get();
    }

    public function popular(?int $exceptId = null): Collection
    {
        return $this->published()
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->orderByDesc('view')
            ->take(5)
            ->get(['id', 'name', 'slug', 'image', 'created_at', 'view']);
    }

    public function paginateForAdmin(?string $search, mixed $categoryId): LengthAwarePaginator
    {
        $query = $this->query()
            ->with('category')
            ->when($categoryId, fn ($q) => $q->where('category_post_id', $categoryId))
            ->latest();

        return $this->paginateWithSearch($query, $search, 'name');
    }
}
