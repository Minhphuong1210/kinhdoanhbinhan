<?php

namespace App\Modules\Core\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryPost;
use App\Models\Order;
use App\Models\Product;
use App\Models\Tag;

class ProductController extends Controller
{
    public function index()
    {
        $categorySlug = request('category');
        $activeCategory = null;

        if ($categorySlug) {
            $activeCategory = Category::where('slug', $categorySlug)->where('active', 1)->first();
        }

        $tags = Tag::with([
            'products' => function ($q) use ($activeCategory) {
                $q->where('active', 1)
                    ->with('category')
                    ->latest();

                if ($activeCategory) {
                    $q->where('category_id', $activeCategory->id);
                }
            }
        ])->get();

        $categories = Category::where('active', 1)->whereNull('parent_id')->orderBy('name')->get();

        return view('Core::Frontend.products.index', compact(
            'tags', 'categories', 'activeCategory'
        ));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('active', 1)
            ->with(['category', 'tags'])
            ->firstOrFail();

        // Tăng view
        $product->increment('view');

        // Sản phẩm liên quan cùng category
        $related = Product::where('active', 1)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        $latestOrders = Order::with('user')
            ->where('status', 'completed')
            ->latest()
            ->take(20)
            ->get();

        $postCategories = CategoryPost::where('active', 1)->get();

        return view('Core::Frontend.products.show', compact(
            'product', 'related', 'latestOrders', 'postCategories'
        ));
    }
}
