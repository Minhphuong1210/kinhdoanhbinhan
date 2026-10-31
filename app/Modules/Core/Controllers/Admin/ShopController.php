<?php

namespace App\Modules\Core\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CoCheHoanPhi;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        // Danh mục gốc active cho tab navbar
        $coCheHoanPhis = CoCheHoanPhi::withCount(['products' => fn($q) => $q->where('active', 1)->availableForPurchase()])
            ->with('parent')
            ->orderBy('parent_id')
            ->orderBy('name')
            ->get();

        // Lấy danh mục/cơ chế đang chọn
        $activeCoCheId = $request->input('co_che');
        $activeCoChe = $activeCoCheId ? CoCheHoanPhi::find($activeCoCheId) : null;
        $activeMode = ($activeCoChe && $activeCoChe->trang_hoat_dong === 'tu_chon' && !$this->isBinhAnZeroPercentCoChe($activeCoChe)) ? 'custom_co_che' : null;
        $activeCoCheIds = $activeCoCheId ? $this->coCheIdsWithChildren((int)$activeCoCheId) : [];
        $activeCatId = $request->input('cat') ?? ($activeCoCheId ? null : 1);
        $activeCategory = $activeCatId ? Category::find($activeCatId) : null;

        $categories = Category::whereNull('parent_id')
            ->where('active', 1)
            ->when($activeCoCheId, function ($q) use ($activeCoCheIds) {
                $q->where(function ($subQuery) use ($activeCoCheIds) {
                    $subQuery->whereHas('products', function ($productQuery) use ($activeCoCheIds) {
                        $productQuery->where('active', 1)
                            ->availableForPurchase()
                            ->whereIn('co_che_hoan_phi_id', $activeCoCheIds);
                    })->orWhereHas('children.products', function ($productQuery) use ($activeCoCheIds) {
                        $productQuery->where('active', 1)
                            ->availableForPurchase()
                            ->whereIn('co_che_hoan_phi_id', $activeCoCheIds);
                    });
                });
            })
            ->orderBy('name')
            ->get();

        $categories->each(function ($category) use ($activeCoCheIds) {
            $categoryIds = Category::where('parent_id', $category->id)->pluck('id')->prepend($category->id);
            $countQuery = Product::where('active', 1)->availableForPurchase()->whereIn('category_id', $categoryIds);

            if (!empty($activeCoCheIds)) {
                $countQuery->whereIn('co_che_hoan_phi_id', $activeCoCheIds);
            }

            $category->products_count = $countQuery->count();
        });

        // Lấy sản phẩm theo danh mục (hoặc tất cả nếu chưa chọn)
        $query = Product::where('active', 1)->availableForPurchase()->with('category', 'coCheHoanPhi')->withCount('children');
        if ($activeCoCheId) {
            $query->whereIn('co_che_hoan_phi_id', $activeCoCheIds);
        }

        if ($activeCatId) {
            // Lấy cả sản phẩm của danh mục con
            $subIds = Category::where('parent_id', $activeCatId)->pluck('id');
            $allIds = $subIds->prepend((int) $activeCatId);
            $query->whereIn('category_id', $allIds);
        }
        $products = $query->orderBy('stt', 'asc')->paginate(16)->withQueryString();

        return view('Core::Admin.shop.index', compact(
            'categories',
            'products',
            'activeCatId',
            'activeCategory',
            'coCheHoanPhis',
            'activeCoCheId',
            'activeCoChe',
            'activeMode'
        ));
    }

    private function coCheIdsWithChildren(int $coCheId): array
    {
        $ids = [$coCheId];
        $currentIds = [$coCheId];

        while (!empty($currentIds)) {
            $childIds = CoCheHoanPhi::whereIn('parent_id', $currentIds)->pluck('id')->all();
            $childIds = array_values(array_diff($childIds, $ids));

            if (empty($childIds)) {
                break;
            }

            $ids = array_merge($ids, $childIds);
            $currentIds = $childIds;
        }

        return $ids;
    }

    private function isBinhAnZeroPercentCoChe(CoCheHoanPhi $coChe): bool
    {
        $source = $this->normalizeSearchText(trim(implode(' ', array_filter([
            $coChe->name,
            $coChe->parent?->name,
        ]))));

        return str_contains($source, 'binh an') && (float) ($coChe->tang_1 ?? 0) == 0.0;
    }

    private function normalizeSearchText(string $text): string
    {
        $normalized = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

        return strtolower($normalized !== false ? $normalized : $text);
    }

    public function buy(Product $product, Request $request)
    {
        abort_unless((int) $product->active === 1 && ($product->quantity === null || (int) $product->quantity > 0), 404);

        //        $provinces = \App\Models\Provinces::orderBy('name')->get(['id', 'name']);
        $provinces = \App\Models\PancakeProvinces::whereNotNull('new_id')->orderBy('name')->get(['id_pancake as id', 'name']);


        $user = auth()->user();

        // mode=custom: lấy sản phẩm con. mode=custom_co_che: lấy sản phẩm cùng cơ chế hoàn phí.
        $mode = $request->input('mode');
        $isCustom = in_array($mode, ['custom', 'custom_co_che'], true);
        $children = collect();
        if ($isCustom) {
            $children = $mode === 'custom_co_che' && $product->co_che_hoan_phi_id
                ? Product::query()
                    ->where('active', 1)
                    ->availableForPurchase()
                    ->where('co_che_hoan_phi_id', $product->co_che_hoan_phi_id)
                    ->with('category')
                    ->orderBy('stt', 'asc')
                    ->orderBy('id', 'asc')
                    ->get()
                : $product->getActiveChildren()->load('category');
        }

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

        $product = Product::findOrFail($data['product_id']);
        $childIds = $data['child_ids'];
        $childQtys = $data['child_qtys'];
        $products = Product::with('category')
            ->availableForPurchase()
            ->whereIn('id', $childIds)
            ->get()
            ->keyBy('id');

        $items = collect();
        foreach ($childIds as $index => $id) {
            $qty = (int)($childQtys[$index] ?? 0);
            $child = $products->get((int)$id);

            if ($qty <= 0 || !$child) {
                continue;
            }

            $items->push([
                'product' => $child,
                'qty' => $qty,
                'line_total' => $child->price * $qty,
            ]);
        }

        if ($items->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['child_ids' => 'Vui long chon it nhat 1 san pham voi so luong lon hon 0.']);
        }

        $shippingFee = \App\Services\OrderService::SHIPPING_FEE_TU_CHON;
        $total = $items->sum('line_total');
        $payableTotal = $total + $shippingFee;
        $totalHoan = max(0, $total - $shippingFee);
        $wallet = (int)(auth()->user()->vi_tien_mua_hang ?? 0);
        $paymentAction = $wallet >= $payableTotal
            ? route('admin.orders.store')
            : route('payos.create-payment-link');

        $province = \App\Models\PancakeProvinces::where('id_pancake', $data['province_id'])->first();
        $district = \App\Models\PancakeDistricts::where('id_pancake', $data['district_id'])->first();
        $ward = \App\Models\PancakeCommunes::where('id_pancake', $data['ward_id'])->first();

        return view('Core::Admin.shop.confirm', compact(
            'data',
            'product',
            'items',
            'total',
            'shippingFee',
            'payableTotal',
            'totalHoan',
            'wallet',
            'paymentAction',
            'province',
            'district',
            'ward'
        ));
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
