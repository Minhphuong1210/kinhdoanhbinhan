<?php

namespace App\Modules\Cart\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Modules\Cart\Exceptions\CartException;
use App\Modules\Cart\Models\CartItem;
use App\Modules\Cart\Requests\AddCartItemRequest;
use App\Modules\Cart\Requests\UpdateCartItemRequest;
use App\Modules\Cart\Services\CartService;
use App\Modules\Shared\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartApiController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly CartService $cartService)
    {
    }

    /**
     * GET /api/cart
     */
    public function index(Request $request): JsonResponse
    {
        return $this->success($this->cartPayload($request));
    }

    /**
     * GET /api/cart/count — tổng số lượng, dùng cho badge giỏ hàng
     */
    public function count(Request $request): JsonResponse
    {
        return $this->success(['count' => $this->cartService->count($request->user())]);
    }

    /**
     * POST /api/cart/items — chỉ nhận sản phẩm thuộc mua tự chọn của combo
     */
    public function store(AddCartItemRequest $request): JsonResponse
    {
        return $this->handle($request, function () use ($request) {
            $data = $request->validated();

            $this->cartService->add(
                $request->user(),
                (int) $data['parent_product_id'],
                (int) $data['product_id'],
                (int) ($data['quantity'] ?? 1),
                $data['mode'] ?? CartService::MODE_CUSTOM
            );

            return $this->success($this->cartPayload($request), 'Da them vao gio hang', 201);
        });
    }

    /**
     * PUT /api/cart/items/{item}
     */
    public function update(UpdateCartItemRequest $request, int $item): JsonResponse
    {
        return $this->handle($request, function () use ($request, $item) {
            $this->cartService->updateQuantity($request->user(), $item, (int) $request->validated('quantity'));

            return $this->success($this->cartPayload($request), 'Da cap nhat gio hang');
        });
    }

    /**
     * DELETE /api/cart/items/{item}
     */
    public function destroy(Request $request, int $item): JsonResponse
    {
        return $this->handle($request, function () use ($request, $item) {
            $this->cartService->remove($request->user(), $item);

            return $this->success($this->cartPayload($request), 'Da xoa san pham khoi gio hang');
        });
    }

    /**
     * DELETE /api/cart?parent_product_id= — bỏ trống để xoá cả giỏ
     */
    public function clear(Request $request): JsonResponse
    {
        $data = $request->validate([
            'parent_product_id' => ['nullable', 'integer'],
        ]);

        $this->cartService->clear($request->user(), isset($data['parent_product_id']) ? (int) $data['parent_product_id'] : null);

        return $this->success($this->cartPayload($request), 'Da xoa gio hang');
    }

    private function handle(Request $request, callable $callback): JsonResponse
    {
        try {
            return $callback();
        } catch (CartException $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 422, $this->cartPayload($request));
        }
    }

    private function cartPayload(Request $request): array
    {
        $groups = $this->cartService->groups($request->user());

        return [
            'groups' => $groups->map(fn (array $group) => [
                'parent_product' => $this->productPayload($group['parent']),
                'mode'           => $group['mode'],
                'items'          => $group['items']->map(fn (CartItem $item) => $this->itemPayload($item))->values(),
                'summary'        => [
                    'total_product_price' => $group['product_total'],
                    'shipping_price'      => $group['shipping_fee'],
                    'payable_total'       => $group['payable_total'],
                ],
                // Gửi kèm thông tin người nhận lên /orders/preview-tu-chon hoặc /orders/mua-tu-chon
                'checkout'       => $group['checkout'],
            ])->values(),
            'total_quantity' => (int) $groups->sum(fn (array $group) => $group['items']->sum('quantity')),
        ];
    }

    private function itemPayload(CartItem $item): array
    {
        return [
            'id'           => $item->id,
            'product_id'   => $item->product_id,
            'quantity'     => $item->quantity,
            'unit_price'   => (float) ($item->product->price ?? 0),
            'line_total'   => (float) $item->line_total,
            'is_available' => (bool) $item->is_available,
            'product'      => $item->product ? $this->productPayload($item->product) : null,
        ];
    }

    private function productPayload(Product $product): array
    {
        return [
            'id'              => $product->id,
            'name'            => $product->name,
            'slug'            => $product->slug,
            'price'           => (float) ($product->price ?? 0),
            'old_price'       => (float) ($product->old_price ?? 0),
            'price_hoan_tien' => (float) ($product->price_hoan_tien ?? 0),
            'quantity'        => $product->quantity,
            'in_stock'        => $product->quantity === null || (int) $product->quantity > 0,
            'image'           => $this->assetUrl($product->image),
            'category'        => $product->relationLoaded('category') && $product->category ? [
                'id'   => $product->category->id,
                'name' => $product->category->name,
            ] : null,
        ];
    }

    private function assetUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return filter_var($path, FILTER_VALIDATE_URL) ? $path : cdn($path);
    }
}
