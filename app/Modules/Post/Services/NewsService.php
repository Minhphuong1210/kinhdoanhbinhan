<?php

namespace App\Modules\Post\Services;

use App\Modules\CategoryPost\Models\CategoryPost;
use App\Modules\Post\Models\Post;
use App\Modules\Post\Repositories\PostRepository;

/**
 * Tin tức trên website công khai.
 */
class NewsService
{
    public function __construct(private readonly PostRepository $posts)
    {
    }

    public function indexData(string $keyword, ?string $slug): array
    {
        $activeCategory = $slug
            ? CategoryPost::where('slug', $slug)->where('active', 1)->first()
            : null;

        return [
            'posts'          => $this->posts->paginatePublished($slug ? ($activeCategory?->id ?? 0) : null, $keyword),
            'featuredPosts'  => $this->posts->published()->where('featured', 1)->latest()->take(3)->get(),
            'categories'     => $this->categoriesWithCount(),
            'activeCategory' => $activeCategory,
            'keyword'        => $keyword,
            'popularPosts'   => $this->posts->popular(),
        ];
    }

    public function detailData(string $slug): array
    {
        $post = $this->posts->published()->where('slug', $slug)->with('category')->firstOrFail();
        $post->increment('view');

        return [
            'post'         => $post,
            'related'      => $this->posts->relatedTo($post, 3),
            'categories'   => $this->categoriesWithCount(),
            'popularPosts' => $this->posts->popular($post->id),
        ];
    }

    private function categoriesWithCount()
    {
        // Đếm theo status 'published' (trước đây đếm status = 1 nên luôn ra 0)
        return CategoryPost::where('active', 1)
            ->withCount(['posts' => fn ($q) => $q->where('status', 'published')->where('vi_tri_web', config('app.vi_tri_web', 'cong_ty'))])
            ->orderBy('name')
            ->get();
    }
}
