<?php

namespace App\Modules\Order\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Modules\Order\Repositories\OrderRepository;
use App\Modules\Order\Services\ShopService;
use App\Modules\Product\Repositories\ProductRepository;
use App\Modules\Product\Services\ProductCatalogService;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __construct(
        private readonly ShopService $shopService,
        private readonly ProductCatalogService $catalog,
        private readonly ProductRepository $products,
        private readonly OrderRepository $orders
    ) {
    }

    public function index(Request $request)
    {
        return view('Core::Admin.shop.index', $this->catalog->shopData($request->input('co_che'), $request->input('cat')));
    }

    public function buy(Product $product, Request $request)
    {
        abort_unless((int) $product->active === 1 && ($product->quantity === null || (int) $product->quantity > 0), 404);

        $provinces = $this->shopService->provinces();
        $user = auth()->user();
        $mode = $request->input('mode');
        $isCustom = in_array($mode, ['custom', 'custom_co_che'], true);
        $children = $isCustom ? $this->products->selectable($product, $mode, ['category']) : collect();

        return view('Core::Admin.shop.buy', compact('product', 'provinces', 'user', 'children', 'isCustom'));
    }

    public function confirm(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'child_ids' => 'required|array',
            'child_qtys' => 'required|array',
            'mode' => 'nullable|in:custom,custom_co_che',
            'receive_user' => 'required|max:255',
            'receive_phone' => 'required|max:20',
            'province_id' => 'required',
            'district_id' => 'required',
            'ward_id' => 'required',
            'receive_address' => 'required|max:500',
            'note' => 'nullable|max:500',
            'confirmed' => 'required|accepted',
        ], [
            'confirmed.required' => 'Ban can tich xac nhan truoc khi tiep tuc.',
            'confirmed.accepted' => 'Ban can tich xac nhan truoc khi tiep tuc.',
        ]);

        $summary = $this->shopService->confirmData($data, auth()->user());

        if ($summary['items']->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['child_ids' => 'Vui long chon it nhat 1 san pham voi so luong lon hon 0.']);
        }

        $product = $this->products->query()->findOrFail($data['product_id']);

        return view('Core::Admin.shop.confirm', compact('data', 'product') + $summary);
    }

    public function thankYou($orderId)
    {
        $order = $this->orders->findByCode($orderId);

        abort_unless(auth()->id() == $order->user_id, 403);

        return view('Core::Admin.shop.thank-you', compact('order'));
    }
}
