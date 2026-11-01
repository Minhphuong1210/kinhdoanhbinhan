<?php

namespace App\Modules\Post\Services;

use App\Modules\CategoryPost\Models\CategoryPost;
use App\Modules\Post\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Tin tức trên API.
 */
class PostApiService
{
    public function newsCategories()
    {
        return CategoryPost::where('active', 1)
            ->withCount(['posts' => fn($query) => $this->publishedPostQuery($query)])
            ->orderBy('name')
            ->get()
            ->map(fn(CategoryPost $category) => [
                'id'          => $category->id,
                'name'        => $category->name,
                'slug'        => $category->slug,
                'posts_count' => $category->posts_count,
            ]);
    }

    public function news(array $filters): LengthAwarePaginator
    {
        $perPage = min((int) ($filters['per_page'] ?? 12), 50);
        $query   = Post::forWeb()->with('category')->latest();
        $this->publishedPostQuery($query);

        if (! empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_post_id', $filters['category_id']);
        }

        if (! empty($filters['category'])) {
            $category = CategoryPost::where('slug', $filters['category'])->where('active', 1)->first();
            $query->where('category_post_id', $category?->id ?? 0);
        }

        if (! empty($filters['featured'])) {
            $query->where('featured', 1);
        }

        return $query->paginate($perPage)->through(fn(Post $post) => $this->postPayload($post));
    }

    public function newsDetail(string $slug): array
    {
        $query = Post::forWeb()
            ->where('slug', $slug)
            ->with('category');
        $this->publishedPostQuery($query);

        $post = $query->firstOrFail();
        $post->increment('view');

        $relatedQuery = Post::forWeb()
            ->where('category_post_id', $post->category_post_id)
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(4);
        $this->publishedPostQuery($relatedQuery);

        return [
            'data'    => $this->postPayload($post, true),
            'related' => $relatedQuery->get()->map(fn(Post $item) => $this->postPayload($item)),
        ];
    }

    private function postPayload(Post $post, bool $withContent = false): array
    {
        $data = [
            'id'          => $post->id,
            'name'        => $post->name,
            'slug'        => $post->slug,
            'image'       => asset_url($post->image),
            'description' => $post->description,
            'view'        => $post->view,
            'featured'    => (int) $post->featured,
            'created_at'  => optional($post->created_at)->toDateTimeString(),
            'category'    => $post->category ? [
                'id'   => $post->category->id,
                'name' => $post->category->name,
                'slug' => $post->category->slug,
            ] : null,
        ];

        if ($withContent) {
            $data['content']          = $post->content;
            $data['meta_title']       = $post->meta_title;
            $data['meta_keywords']    = $post->meta_keywords;
            $data['meta_description'] = $post->meta_description;
        }

        return $data;
    }

    private function publishedPostQuery($query): void
    {
        $query->whereIn('status', ['published', 1, '1']);
    }
}
