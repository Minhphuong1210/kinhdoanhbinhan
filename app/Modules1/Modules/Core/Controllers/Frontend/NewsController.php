<?php

namespace App\Modules\Core\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CategoryPost;
use App\Models\Order;
use App\Models\Post;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {

        $query = Post::forWeb()
            ->where('status', 'published')
            ->with('category')
            ->latest();

        if ($request->filled('slug')) {
            $category = CategoryPost::where('slug', $request->slug)->first();

            if ($category) {
                $query->where('category_post_id', $category->id);
            } else {
                // slug không tồn tại -> có thể trả rỗng hoặc bỏ qua
                $query->whereRaw('1 = 0'); // trả về rỗng
            }
        }

        $posts = $query->paginate(12);

//        dd($posts);

        $categories = CategoryPost::withCount(['posts' => fn($q) => $q->where('status', 1)])
            ->where('active', 1)
            ->get();

        $featuredPosts = Post::forWeb()
            ->where('status', 'published')
            ->where('featured', 1)
            ->latest()
            ->take(3)
            ->get();

        $latestOrders = Order::with('user')
            ->where('status', 'completed')
            ->latest()
            ->take(20)
            ->get();

        return view('Core::Frontend.news.index', compact(
            'posts', 'categories', 'featuredPosts', 'latestOrders'
        ));
    }

    public function show($slug)
    {
        $post = Post::forWeb()
            ->where('slug', $slug)
            ->where('status', 'published')

            ->with('category')
            ->firstOrFail();

        $post->increment('view');

        // Bài viết cùng danh mục
        $related = Post::forWeb()
            ->where('status', 'published')

            ->where('category_post_id', $post->category_post_id)
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(4)
            ->get();

        $categories = CategoryPost::withCount(['posts' => fn($q) => $q->where('status', 1)])
            ->where('active', 1)
            ->get();

        $latestOrders = Order::with('user')
            ->where('status', 'completed')
            ->latest()
            ->take(20)
            ->get();

        return view('Core::Frontend.news.show', compact(
            'post', 'related', 'categories', 'latestOrders'
        ));
    }
}
