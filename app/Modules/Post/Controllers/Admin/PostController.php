<?php

namespace App\Modules\Post\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryPost;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:post.view')->only(['index']);
        $this->middleware('permission:post.create')->only(['create', 'store']);
        $this->middleware('permission:post.edit')->only(['edit', 'update', 'image']);
        $this->middleware('permission:post.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Post::with('category');
        if ($search = $request->search) {
            $query->where('name', 'like', "%$search%");
        }
        if ($request->filled('category_post_id')) {
            $query->where('category_post_id', $request->category_post_id);
        }
        $posts      = $query->latest()->paginate(20)->withQueryString();
        $categories = CategoryPost::all();
        return view('Post::Admin.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $categories = CategoryPost::all();
        return view('Post::Admin.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|max:255',
            'category_post_id' => 'nullable|exists:category_posts,id',
            'image'            => 'nullable|image|max:3072',
            'description'      => 'nullable|string',
            'content'          => 'nullable|string',
            'meta_title'       => 'nullable|max:255',
            'meta_keywords'    => 'nullable|max:255',
            'meta_description' => 'nullable|max:500',
            'vi_tri_web'       => 'required|in:htx,cong_ty',
        ]);
        $data['slug']     = Str::slug($data['name']);
        $data['status']   = $request->get('status', 'draft');
        $data['featured'] = $request->boolean('featured');
        $data['admin_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $data['image'] = cdn_upload($request->file('image'), 'posts');
        }

        Post::create($data);
        return redirect()->route('admin.posts.index')->with('success', 'Thêm bài viết thành công!');
    }

    public function edit(Post $post)
    {
        $categories = CategoryPost::all();
        return view('Post::Admin.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'name'             => 'required|max:255',
            'category_post_id' => 'nullable|exists:category_posts,id',
            'image'            => 'nullable|image|max:3072',
            'description'      => 'nullable|string',
            'content'          => 'nullable|string',
            'meta_title'       => 'nullable|max:255',
            'meta_keywords'    => 'nullable|max:255',
            'meta_description' => 'nullable|max:500',
            'vi_tri_web'       => 'required|in:htx,cong_ty',
        ]);
        $data['slug']     = Str::slug($data['name']);
        $data['status']   = $request->get('status', 'draft');
        $data['featured'] = $request->boolean('featured');
        if ($request->hasFile('image')) {
            $data['image'] = cdn_upload($request->file('image'), 'posts');
        }
        $post->update($data);
        return redirect()->route('admin.posts.index')->with('success', 'Cập nhật bài viết thành công!');
    }

    public function image(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ]);

        $path = cdn_upload(
            $request->file('upload'),
            'posts/content'
        );

        return response()->json([
            'uploaded' => 1,
            'fileName' => basename($path),
            'url' => cdn($path),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Xóa ảnh trong CKEditor
    |--------------------------------------------------------------------------
    */

    private function deleteEditorImages(?string $html): void
    {
        if (empty($html)) {
            return;
        }

        preg_match_all(
            '/<img[^>]+src=["\']([^"\']+)["\']/i',
            $html,
            $matches
        );

        if (empty($matches[1])) {
            return;
        }

        foreach ($matches[1] as $url) {

            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                continue;
            }

            cdn_delete($url);
        }
    }


    public function destroy(Post $post)
    {
        /*
        |--------------------------------------------------------------------------
        | Xóa ảnh đại diện
        |--------------------------------------------------------------------------
        */

        if ($post->image) {
            cdn_delete($post->image);
        }

        

        $this->deleteEditorImages($post->content);


        $this->deleteEditorImages($post->description);

        $post->delete();


        return back()
            ->with(
                'success',
                'Xóa bài viết và toàn bộ hình ảnh thành công!'
            );
    }
}
