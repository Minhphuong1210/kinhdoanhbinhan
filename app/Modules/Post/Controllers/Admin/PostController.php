<?php

namespace App\Modules\Post\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\CategoryPost\Services\CategoryPostService;
use App\Modules\Post\Models\Post;
use App\Modules\Post\Requests\PostRequest;
use App\Modules\Post\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService,
        private readonly CategoryPostService $categoryPostService
    ) {
        $this->middleware('permission:post.view')->only(['index']);
        $this->middleware('permission:post.create')->only(['create', 'store']);
        $this->middleware('permission:post.edit')->only(['edit', 'update', 'image']);
        $this->middleware('permission:post.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $posts = $this->postService->list($request->search, $request->category_post_id);
        $categories = $this->categoryPostService->all();

        return view('Post::Admin.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $categories = $this->categoryPostService->all();

        return view('Post::Admin.create', compact('categories'));
    }

    public function store(PostRequest $request)
    {
        $this->postService->create($request->validated() + ['admin_id' => auth()->id()]);

        return redirect()->route('admin.posts.index')->with('success', 'Thêm bài viết thành công!');
    }

    public function edit(Post $post)
    {
        $categories = $this->categoryPostService->all();

        return view('Post::Admin.edit', compact('post', 'categories'));
    }

    public function update(PostRequest $request, Post $post)
    {
        $this->postService->update($post, $request->validated());

        return redirect()->route('admin.posts.index')->with('success', 'Cập nhật bài viết thành công!');
    }

    public function image(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ]);

        $path = $this->postService->uploadContentImage($request->file('upload'));

        return response()->json([
            'uploaded' => 1,
            'fileName' => basename($path),
            'url' => cdn($path),
        ]);
    }

    public function destroy(Post $post)
    {
        $this->postService->delete($post);

        return back()->with('success', 'Xóa bài viết và toàn bộ hình ảnh thành công!');
    }
}
