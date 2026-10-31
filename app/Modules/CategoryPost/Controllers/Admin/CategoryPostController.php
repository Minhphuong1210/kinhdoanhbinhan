<?php

namespace App\Modules\CategoryPost\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\CategoryPost\Models\CategoryPost;
use App\Modules\CategoryPost\Requests\CategoryPostRequest;
use App\Modules\CategoryPost\Services\CategoryPostService;
use Illuminate\Http\Request;

class CategoryPostController extends Controller
{
    public function __construct(private readonly CategoryPostService $categoryPostService)
    {
        $this->middleware('permission:product.view')->only(['index']);
        $this->middleware('permission:product.create')->only(['create', 'store']);
        $this->middleware('permission:product.edit')->only(['edit', 'update', 'image']);
        $this->middleware('permission:product.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $categories = $this->categoryPostService->list($request->search);

        return view('CategoryPost::Admin.index', compact('categories'));
    }

    public function create()
    {
        return view('CategoryPost::Admin.create');
    }

    public function store(CategoryPostRequest $request)
    {
        $this->categoryPostService->create($request->validated());

        return redirect()->route('admin.category-posts.index')->with('success', 'Thêm thành công!');
    }

    public function edit(CategoryPost $categoryPost)
    {
        return view('CategoryPost::Admin.edit', compact('categoryPost'));
    }

    public function update(CategoryPostRequest $request, CategoryPost $categoryPost)
    {
        $this->categoryPostService->update($categoryPost, $request->validated());

        return redirect()->route('admin.category-posts.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(CategoryPost $categoryPost)
    {
        $this->categoryPostService->delete($categoryPost);

        return back()->with('success', 'Xóa thành công!');
    }
}
