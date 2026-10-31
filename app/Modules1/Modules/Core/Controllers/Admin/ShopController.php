<?php

namespace App\Modules\Core\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        // Danh mục gốc active cho tab navbar
        $categories = Category::whereNull('parent_id')
            ->where('active', 1)
            ->withCount(['products' => fn($q) => $q->where('active', 1)])
            ->orderBy('name')
            ->get();

        // Lấy danh mục đang chọn
        $activeCatId = $request->input('cat') ?? 1;
        $activeCategory = $activeCatId ? Category::find($activeCatId) : null;

        // Lấy sản phẩm theo danh mục (hoặc tất cả nếu chưa chọn)
        $query = Product::where('active', 1)->with('category')->withCount('children');
        if ($activeCatId) {
            // Lấy cả sản phẩm của danh mục con
            $subIds = Category::where('parent_id', $activeCatId)->pluck('id');
            $allIds = $subIds->prepend((int)$activeCatId);
            $query->whereIn('category_id', $allIds);
        }
        $products = $query->orderBy('stt','asc')->paginate(16)->withQueryString();

        return view('Core::Admin.shop.index', compact('categories', 'products', 'activeCatId', 'activeCategory'));
    }

    public function buy(Product $product, Request $request)
    {
//        $provinces = \App\Models\Provinces::orderBy('name')->get(['id', 'name']);
        $provinces = \App\Models\PancakeProvinces::whereNotNull('new_id')->orderBy('name')->get(['id_pancake as id', 'name']);


        $user      = auth()->user();

        // Kiểm tra xem user có chọn "Mua tự chọn" hay không
        $isCustom = $request->input('mode') === 'custom';
        $children = $isCustom ? $product->getActiveChildren() : collect();

        return view('Core::Admin.shop.buy', compact('product', 'provinces', 'user', 'children', 'isCustom'));
    }

    public function thankYou($orderId)
    {
        $order = \App\Models\Order::where('ma_don_hang', $orderId)->firstOrFail();



        $user = auth()->user();

        if ($user->id == $order->user_id) {
            return view('Core::Admin.shop.thank-you', compact('order'));

        }
        return abort(403);

    }

}
