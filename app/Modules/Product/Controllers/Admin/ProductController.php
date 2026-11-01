<?php

namespace App\Modules\Product\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Modules\Category\Services\CategoryService;
use App\Modules\Product\Requests\ProductRequest;
use App\Modules\Product\Services\CoCheHoanPhiService;
use App\Modules\Product\Services\ProductService;
use App\Modules\Tag\Services\TagService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
        private readonly CategoryService $categoryService,
        private readonly TagService $tagService,
        private readonly CoCheHoanPhiService $coCheService
    ) {
        $this->middleware('permission:product.view')->only(['index']);
        $this->middleware('permission:product.create')->only(['create', 'store']);
        $this->middleware('permission:product.edit')->only(['edit', 'update', 'image']);
        $this->middleware('permission:product.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $products = $this->productService->list($request->search, $request->category_id);
        $categories = $this->categoryService->rootCategories();

        return view('Product::Admin.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = $this->categoryService->all();
        $tags = $this->tagService->all();
        $products = $this->productService->options();
        $coCheHoanPhis = $this->coCheService->options();

        return view('Product::Admin.create', compact('categories', 'tags', 'products', 'coCheHoanPhis'));
    }

    public function store(ProductRequest $request)
    {
        $this->productService->create($request->validated());

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function edit(Product $product)
    {
        $categories = $this->categoryService->all();
        $tags = $this->tagService->all();
        $products = $this->productService->options($product->id);
        $coCheHoanPhis = $this->coCheService->options();
        $extraImages = $product->image_extra ?? []; // already decoded via cast
        $product->load('tags');

        return view('Product::Admin.edit', compact('product', 'categories', 'tags', 'extraImages', 'products', 'coCheHoanPhis'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $this->productService->update($product, $request->validated());

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy(Product $product)
    {
        $this->productService->delete($product);

        return back()->with('success', 'Xóa sản phẩm và toàn bộ hình ảnh thành công!');
    }

    public function image(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ]);

        $path = $this->productService->uploadContentImage($request->file('upload'));

        return response()->json([
            'uploaded' => 1,
            'fileName' => basename($path),
            'url'      => cdn($path),
        ]);
    }
}
