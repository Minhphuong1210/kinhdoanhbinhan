<?php

namespace App\Modules\Promotion\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gift;
use App\Models\ProductPromotion;
use App\Models\Promotion;
use App\Modules\Product\Repositories\ProductRepository;
use App\Modules\Promotion\Requests\GiftRequest;
use App\Modules\Promotion\Requests\PromotionRequest;
use App\Modules\Promotion\Services\PromotionService;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function __construct(
        private readonly PromotionService $promotionService,
        private readonly ProductRepository $productRepository
    ) {
    }

    /**
     * Danh sách sự kiện khuyến mãi
     */
    public function index()
    {
        $promotions = $this->promotionService->list();

        return view('Promotion::Admin.index', compact('promotions'));
    }

    /**
     * Form tạo sự kiện mới
     */
    public function create()
    {
        $products = $this->productRepository->activeOrderedByName();

        return view('Promotion::Admin.create', compact('products'));
    }

    /**
     * Lưu sự kiện mới
     */
    public function store(PromotionRequest $request)
    {
        $promotion = $this->promotionService->create($request->validated());

        return redirect()->route('admin.promotions.edit', $promotion->id)
            ->with('success', 'Tạo sự kiện thành công! Bây giờ hãy thêm sản phẩm và quà tặng.');
    }

    /**
     * Form sửa sự kiện + quản lý sản phẩm + quà tặng
     */
    public function edit(Promotion $promotion)
    {
        $promotion = $this->promotionService->loadForEdit($promotion);
        $products = $this->productRepository->activeOrderedByName();

        return view('Promotion::Admin.edit', compact('promotion', 'products'));
    }

    /**
     * Cập nhật thông tin sự kiện
     */
    public function update(PromotionRequest $request, Promotion $promotion)
    {
        $this->promotionService->update($promotion, $request->validated());

        return back()->with('success', 'Cập nhật sự kiện thành công!');
    }

    /**
     * Xoá sự kiện
     */
    public function destroy(Promotion $promotion)
    {
        $this->promotionService->delete($promotion);

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

        if (!$this->promotionService->addProduct($promotion, $request->product_id)) {
            return back()->with('error', 'Sản phẩm này đã có trong sự kiện.');
        }

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
    public function addGift(GiftRequest $request, ProductPromotion $productPromotion)
    {
        $this->promotionService->addGift($productPromotion, $request->validated());

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
