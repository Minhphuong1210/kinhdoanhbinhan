<?php

namespace App\Modules\CategoryPost\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryPostController extends Controller
{

 public function __construct()
    {
        $this->middleware('permission:product.view')->only(['index']);
        $this->middleware('permission:product.create')->only(['create', 'store']);
        $this->middleware('permission:product.edit')->only(['edit', 'update', 'image']);
        $this->middleware('permission:product.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = CategoryPost::withCount('posts');
        if ($search = $request->search) {
            $query->where('name', 'like', "%$search%");
        }
        $categories = $query->latest()->paginate(20)->withQueryString();
        return view('CategoryPost::Admin.index', compact('categories'));
    }

    public function create()
    {
        return view('CategoryPost::Admin.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|max:255', 'active' => 'nullable|boolean']);
        $data['slug']   = Str::slug($data['name']);
        $data['active'] = $request->boolean('active');
        CategoryPost::create($data);
        return redirect()->route('admin.category-posts.index')->with('success', 'Thêm thành công!');
    }

    public function edit(CategoryPost $categoryPost)
    {
        return view('CategoryPost::Admin.edit', compact('categoryPost'));
    }

    public function update(Request $request, CategoryPost $categoryPost)
    {
        $data = $request->validate(['name' => 'required|max:255', 'active' => 'nullable|boolean']);
        $data['slug']   = Str::slug($data['name']);
        $data['active'] = $request->boolean('active');
        $categoryPost->update($data);
        return redirect()->route('admin.category-posts.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(CategoryPost $categoryPost)
    {
        $categoryPost->delete();
        return back()->with('success', 'Xóa thành công!');
    }
}
