<?php

namespace App\Modules\Core\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public const SORTS = [
        'moi-nhat'  => 'Mới nhất',
        'gia-tang'  => 'Giá thấp → cao',
        'gia-giam'  => 'Giá cao → thấp',
        'xem-nhieu' => 'Xem nhiều nhất',
    ];

    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('q'));
        $sort    = array_key_exists($request->query('sort'), self::SORTS) ? $request->query('sort') : 'moi-nhat';

        $activeCategory = $request->filled('category')
            ? Category::where('slug', $request->query('category'))->where('active', 1)->first()
            : null;

        $categoryIds = $activeCategory
            ? Category::where('parent_id', $activeCategory->id)->pluck('id')->push($activeCategory->id)->all()
            : null;

        $products = Product::where('active', 1)
            ->availableForPurchase()
            ->with('category')
            ->when($categoryIds, fn ($q) => $q->whereIn('category_id', $categoryIds))
            ->when($keyword !== '', function ($q) use ($keyword) {
                $q->where(function ($inner) use ($keyword) {
                    $inner->where('name', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhere('slug', 'like', "%{$keyword}%");
                });
            })
            ->when($sort === 'gia-tang', fn ($q) => $q->orderBy('price'))
            ->when($sort === 'gia-giam', fn ($q) => $q->orderByDesc('price'))
            ->when($sort === 'xem-nhieu', fn ($q) => $q->orderByDesc('view'))
            ->latest('id')
            ->paginate(24)
            ->withQueryString();

        $categories = Category::where('active', 1)
            ->whereNull('parent_id')
            ->withCount(['products' => fn ($q) => $q->where('active', 1)->availableForPurchase()])
            ->orderBy('name')
            ->get();

        $sorts = self::SORTS;

        return view('Core::Frontend.products.index', compact(
            'products', 'categories', 'activeCategory', 'keyword', 'sort', 'sorts'
        ));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('active', 1)
            ->availableForPurchase()
            ->with(['category', 'tags'])
            ->withCount(['children as active_children_count' => fn ($q) => $q->where('active', 1)->availableForPurchase()])
            ->firstOrFail();

        $product->increment('view');

        $related = Product::where('active', 1)
            ->availableForPurchase()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest('id')
            ->take(8)
            ->get();

        return view('Core::Frontend.products.show', compact('product', 'related'));
    }
}
