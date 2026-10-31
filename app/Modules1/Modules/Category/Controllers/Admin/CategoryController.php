<?php

namespace App\Modules\Category\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with('parent')->whereNull('parent_id');
        if ($search = $request->search) {
            $query->where('name', 'like', "%$search%");
        }
        $categories = $query->latest()->paginate(20)->withQueryString();
        return view('Category::Admin.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')->get();
        return view('Category::Admin.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'image'     => 'nullable|image|max:2048',
            'active'    => 'nullable|boolean',
        ]);
        $data['slug']   = Str::slug($data['name']);
        $data['active'] = $request->boolean('active');
        if ($request->hasFile('image')) {
            $data['image'] = cdn_upload($request->file('image'), 'categories');
        }
        Category::create($data);
        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công!');
    }

    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')->where('id', '!=', $category->id)->get();
        return view('Category::Admin.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'      => 'required|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'image'     => 'nullable|image|max:2048',
            'active'    => 'nullable|boolean',
        ]);
        $data['slug']   = Str::slug($data['name']);
        $data['active'] = $request->boolean('active');
        if ($request->hasFile('image')) {
            $data['image'] = cdn_upload($request->file('image'), 'categories');
        }
        $category->update($data);
        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success', 'Xóa danh mục thành công!');
    }
}
