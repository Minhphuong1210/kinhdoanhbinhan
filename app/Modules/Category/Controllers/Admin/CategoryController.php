<?php

namespace App\Modules\Category\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Category\Models\Category;
use App\Modules\Category\Requests\CategoryRequest;
use App\Modules\Category\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categoryService)
    {
        $this->middleware('permission:category.view')->only(['index']);
        $this->middleware('permission:category.create')->only(['create', 'store']);
        $this->middleware('permission:category.edit')->only(['edit', 'update']);
        $this->middleware('permission:category.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $categories = $this->categoryService->list($request->search);

        return view('Category::Admin.index', compact('categories'));
    }

    public function create()
    {
        $parents = $this->categoryService->rootCategories();

        return view('Category::Admin.create', compact('parents'));
    }

    public function store(CategoryRequest $request)
    {
        $this->categoryService->create($request->validated());

        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công!');
    }

    public function edit(Category $category)
    {
        $parents = $this->categoryService->rootCategories($category->id);

        return view('Category::Admin.edit', compact('category', 'parents'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $this->categoryService->update($category, $request->validated());

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công!');
    }

    public function destroy(Category $category)
    {
        $this->categoryService->delete($category);

        return back()->with('success', 'Xóa danh mục thành công!');
    }
}
