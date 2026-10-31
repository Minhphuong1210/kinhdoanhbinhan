<?php

namespace App\Modules\Promotion\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gift;
use App\Models\Product;
use App\Models\ProductPromotion;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /**
     * Danh sách sự kiện khuyến mãi
     */
    public function index()
    {
        $promotions = Promotion::withCount('productPromotions')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate(15);

        return view('Promotion::Admin.index', compact('promotions'));
    }

    /**
     * Form tạo sự kiện mới
     */
    public function create()
    {
        $products = Product::where('active', 1)->orderBy('name')->get();
        return view('Promotion::Admin.create', compact('products'));
    }

    /**
     * Lưu sự kiện mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:2020|max:2099',
        ]);

        $promotion = Promotion::create($request->only('name', 'month', 'year', 'description', 'is_active'));

        return redirect()->route('admin.promotions.edit', $promotion->id)
            ->with('success', 'Tạo sự kiện thành công! Bây giờ hãy thêm sản phẩm và quà tặng.');
    }

    /**
     * Form sửa sự kiện + quản lý sản phẩm + quà tặng
     */
    public function edit(Promotion $promotion)
    {
        $promotion->load('productPromotions.product', 'productPromotions.gifts.giftProduct');
        $products = Product::where('active', 1)->orderBy('name')->get();

        return view('Promotion::Admin.edit', compact('promotion', 'products'));
    }

    /**
     * Cập nhật thông tin sự kiện
     */
    public function update(Request $request, Promotion $promotion)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:2020|max:2099',
        ]);

        $promotion->update([
            'name'        => $request->name,
            'month'       => $request->month,
            'year'        => $request->year,
            'description' => $request->description,
            'is_active'   => $request->has('is_active') ? 1 : 0,
        ]);

        return back()->with('success', 'Cập nhật sự kiện thành công!');
    }

    /**
     * Xoá sự kiện
     */
    public function destroy(Promotion $promotion)
    {
        $promotion->delete();
        return redirect()->route('admin.promotions.index')->with('success', 'Đã xoá sự kiện.');
    }

    // ================================================================
    //  QUẢN LÝ SẢN PHẨM TRONG SỰ KIỆN
    // ================================================================

    /**
     * Thêm sản phẩm vào sự kiện
     */
    public function addProduct(Request $request, Promotion $promotion)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        // Kiểm tra trùng
        $exists = ProductPromotion::where('promotion_id', $promotion->id)
            ->where('product_id', $request->product_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Sản phẩm này đã có trong sự kiện.');
        }

        ProductPromotion::create([
            'promotion_id' => $promotion->id,
            'product_id'   => $request->product_id,
        ]);

        return back()->with('success', 'Đã thêm sản phẩm vào sự kiện.');
    }

    /**
     * Xoá sản phẩm khỏi sự kiện (cascade xoá cả gifts)
     */
    public function removeProduct(Promotion $promotion, ProductPromotion $productPromotion)
    {
        $productPromotion->delete();
        return back()->with('success', 'Đã xoá sản phẩm khỏi sự kiện.');
    }

    // ================================================================
    //  QUẢN LÝ QUÀ TẶNG / KHUYẾN MÃI
    // ================================================================

    /**
     * Thêm quà tặng cho product_promotion
     */
    public function addGift(Request $request, ProductPromotion $productPromotion)
    {
        $request->validate([
            'type' => 'required|in:product,discount_percent,discount_amount',
        ]);

        $data = [
            'product_promotion_id' => $productPromotion->id,
            'type'                 => $request->type,
        ];

        if ($request->type === 'product') {
            $request->validate([
                'gift_product_id' => 'required|exists:products,id',
                'quantity'        => 'required|integer|min:1',
            ]);
            $data['gift_product_id'] = $request->gift_product_id;
            $data['quantity']        = $request->quantity;
        } else {
            $request->validate([
                'discount_value' => 'required|numeric|min:0.01',
            ]);
            $data['discount_value'] = $request->discount_value;
        }

        Gift::create($data);

        return back()->with('success', 'Đã thêm khuyến mãi.');
    }

    /**
     * Xoá quà tặng
     */
    public function removeGift(Gift $gift)
    {
        $gift->delete();
        return back()->with('success', 'Đã xoá khuyến mãi.');
    }
}
