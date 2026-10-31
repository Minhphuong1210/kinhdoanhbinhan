<?php

namespace App\Modules\Cart\Services;

use App\Models\Product;
use App\Models\User;
use App\Modules\Cart\Exceptions\CartException;
use App\Modules\Cart\Models\CartItem;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Giỏ hàng chỉ dành cho mua tự chọn: mỗi sản phẩm trong giỏ phải nằm trong
 * danh sách được chọn của một combo (giống trang mua tự chọn).
 */
class CartService
{
    public const MODE_CUSTOM         = 'custom';
    public const MODE_CUSTOM_CO_CHE  = 'custom_co_che';
    public const MODES               = [self::MODE_CUSTOM, self::MODE_CUSTOM_CO_CHE];
    public const MAX_QTY_PER_PRODUCT = 99;

    /**
     * Sản phẩm được phép chọn trong combo:
     * - custom: sản phẩm con đang bán của combo
     * - custom_co_che: sản phẩm đang bán cùng cơ chế hoàn phí với combo
     */
    public function selectableProductsQuery(Product $parent, string $mode): Builder
    {
        if ($mode === self::MODE_CUSTOM_CO_CHE && $parent->co_che_hoan_phi_id) {
            return Product::query()->selectableInCoChe((int) $parent->co_che_hoan_phi_id);
        }

        return Product::query()->where('active', 1)->availableForPurchase()->where('parent_id', $parent->id);
    }

    /**
     * Giỏ của user, gom theo combo. Mỗi nhóm có thể đem đi đặt bằng /orders/mua-tu-chon.
     *
     * @return Collection<int, array{parent: Product, mode: string, items: Collection, ...}>
     */
    public function groups(User $user): Collection
    {
        $items = CartItem::with(['product.category', 'product.coCheHoanPhi', 'parentProduct'])
            ->where('user_id', $user->id)
            ->orderBy('id')
            ->get();

        return $items
            ->filter(fn (CartItem $item) => $item->parentProduct !== null)
            ->groupBy('parent_product_id')
            ->map(fn (Collection $groupItems) => $this->buildGroup($groupItems))
            ->values();
    }

    public function add(User $user, int $parentProductId, int $productId, int $quantity, string $mode): CartItem
    {
        $parent = Product::where('active', 1)->find($parentProductId);
        if (!$parent) {
            throw new CartException('Combo không tồn tại hoặc đã ngừng bán.', 404);
        }

        if ($mode === self::MODE_CUSTOM_CO_CHE && !$parent->co_che_hoan_phi_id) {
            throw new CartException('Combo này không hỗ trợ mua tự chọn theo cơ chế.');
        }

        $product = $this->findSelectableProduct($parent, $mode, $productId);

        return DB::transaction(function () use ($user, $parent, $product, $quantity, $mode) {
            $groupMode = CartItem::where('user_id', $user->id)
                ->where('parent_product_id', $parent->id)
                ->value('mode');

            if ($groupMode && $groupMode !== $mode) {
                throw new CartException('Giỏ hàng của combo này đang ở chế độ khác. Vui lòng xoá giỏ của combo trước khi đổi chế độ.');
            }

            $item = CartItem::where('user_id', $user->id)
                ->where('parent_product_id', $parent->id)
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->first()
                ?? new CartItem([
                    'user_id'           => $user->id,
                    'parent_product_id' => $parent->id,
                    'product_id'        => $product->id,
                    'mode'              => $mode,
                    'quantity'          => 0,
                ]);

            $newQty = $item->quantity + $quantity;
            $this->ensureQuantity($product, $newQty);

            $item->quantity = $newQty;
            $item->save();

            return $item;
        });
    }

    public function updateQuantity(User $user, int $itemId, int $quantity): CartItem
    {
        $item = $this->findUserItem($user, $itemId);
        $parent = $item->parentProduct;

        if (!$parent || (int) $parent->active !== 1) {
            throw new CartException('Combo không tồn tại hoặc đã ngừng bán.');
        }

        $product = $this->findSelectableProduct($parent, $item->mode, $item->product_id);
        $this->ensureQuantity($product, $quantity);

        $item->update(['quantity' => $quantity]);

        return $item;
    }

    public function remove(User $user, int $itemId): void
    {
        $this->findUserItem($user, $itemId)->delete();
    }

    /**
     * Xoá cả giỏ, hoặc chỉ giỏ của một combo.
     */
    public function clear(User $user, ?int $parentProductId = null): void
    {
        CartItem::where('user_id', $user->id)
            ->when($parentProductId, fn ($q) => $q->where('parent_product_id', $parentProductId))
            ->delete();
    }

    /**
     * Bỏ khỏi giỏ những sản phẩm vừa đặt mua thành công.
     */
    public function removePurchased(User $user, int $parentProductId, array $productIds): void
    {
        if (empty($productIds)) {
            return;
        }

        CartItem::where('user_id', $user->id)
            ->where('parent_product_id', $parentProductId)
            ->whereIn('product_id', $productIds)
            ->delete();
    }

    public function count(User $user): int
    {
        return (int) CartItem::where('user_id', $user->id)->sum('quantity');
    }

    private function buildGroup(Collection $items): array
    {
        /** @var CartItem $first */
        $first  = $items->first();
        $parent = $first->parentProduct;
        $mode   = $first->mode ?: self::MODE_CUSTOM;

        $selectableIds = $this->selectableProductsQuery($parent, $mode)
            ->whereIn('id', $items->pluck('product_id'))
            ->pluck('id')
            ->flip();

        $items->each(function (CartItem $item) use ($selectableIds, $parent) {
            $product = $item->product;
            $item->setAttribute('is_available', (int) $parent->active === 1
                && $product
                && $selectableIds->has($product->id)
                && ($product->quantity === null || $product->quantity >= $item->quantity));
            $item->setAttribute('line_total', $product ? (float) $product->price * $item->quantity : 0);
        });

        $available    = $items->where('is_available', true);
        $productTotal = (float) $available->sum('line_total');
        $shippingFee  = $available->isNotEmpty() ? OrderService::SHIPPING_FEE_TU_CHON : 0;

        return [
            'parent'        => $parent,
            'mode'          => $mode,
            'items'         => $items->values(),
            'product_total' => $productTotal,
            'shipping_fee'  => $shippingFee,
            'payable_total' => $productTotal + $shippingFee,
            'checkout'      => [
                'product_id' => $parent->id,
                'mode'       => $mode,
                'items'      => $available->map(fn (CartItem $item) => [
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                ])->values(),
            ],
        ];
    }

    private function findSelectableProduct(Product $parent, string $mode, int $productId): Product
    {
        $product = $this->selectableProductsQuery($parent, $mode)->find($productId);

        if (!$product) {
            throw new CartException('Sản phẩm này không thuộc danh sách mua tự chọn của combo hoặc đã hết hàng.');
        }

        return $product;
    }

    private function findUserItem(User $user, int $itemId): CartItem
    {
        $item = CartItem::with('parentProduct')->where('user_id', $user->id)->find($itemId);

        if (!$item) {
            throw new CartException('Không tìm thấy sản phẩm trong giỏ hàng.', 404);
        }

        return $item;
    }

    private function ensureQuantity(Product $product, int $quantity): void
    {
        if ($quantity < 1) {
            throw new CartException('Số lượng phải lớn hơn 0.');
        }

        if ($quantity > self::MAX_QTY_PER_PRODUCT) {
            throw new CartException('Mỗi sản phẩm chỉ được chọn tối đa ' . self::MAX_QTY_PER_PRODUCT . '.');
        }

        if ($product->quantity !== null && $product->quantity < $quantity) {
            throw new CartException('Sản phẩm "' . $product->name . '" chỉ còn ' . $product->quantity . ' trong kho.');
        }
    }
}
