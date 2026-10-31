<?php

namespace App\Modules\Core\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Modules\CategoryPost\Models\CategoryPost;
use App\Models\Post;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $keyword        = trim((string) $request->query('q'));
        $activeCategory = $request->filled('slug')
            ? CategoryPost::where('slug', $request->query('slug'))->where('active', 1)->first()
            : null;

        $posts = $this->publishedQuery()
            ->with('category')
            ->when($request->filled('slug'), fn ($q) => $q->where('category_post_id', $activeCategory?->id ?? 0))
            ->when($keyword !== '', fn ($q) => $q->where('name', 'like', "%{$keyword}%"))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $featuredPosts = $this->publishedQuery()
            ->where('featured', 1)
            ->latest()
            ->take(3)
            ->get();

        $categories = $this->categoriesWithCount();
        $popularPosts = $this->popularPosts();

        return view('Core::Frontend.news.index', compact(
            'posts', 'categories', 'featuredPosts', 'activeCategory', 'keyword', 'popularPosts'
        ));
    }

    public function show($slug)
    {
        $post = $this->publishedQuery()
            ->where('slug', $slug)
            ->with('category')
            ->firstOrFail();

        $post->increment('view');

        $related = $this->publishedQuery()
            ->where('category_post_id', $post->category_post_id)
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(3)
            ->get();

        $categories   = $this->categoriesWithCount();
        $popularPosts = $this->popularPosts($post->id);

        return view('Core::Frontend.news.show', compact('post', 'related', 'categories', 'popularPosts'));
    }

    private function publishedQuery()
    {
        return Post::forWeb()->where('status', 'published');
    }

    private function categoriesWithCount()
    {
        // Đếm theo status 'published' (trước đây đếm status = 1 nên luôn ra 0)
        return CategoryPost::where('active', 1)
            ->withCount(['posts' => fn ($q) => $q->where('status', 'published')->where('vi_tri_web', config('app.vi_tri_web', 'cong_ty'))])
            ->orderBy('name')
            ->get();
    }

    private function popularPosts(?int $exceptId = null)
    {
        return $this->publishedQuery()
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->orderByDesc('view')
            ->take(5)
            ->get(['id', 'name', 'slug', 'image', 'created_at', 'view']);
    }
}
