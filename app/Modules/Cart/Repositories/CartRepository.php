<?php

namespace App\Modules\Cart\Repositories;

use App\Models\User;
use App\Modules\Cart\Models\CartItem;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CartRepository extends BaseRepository
{
    protected string $model = CartItem::class;

    public function forUser(User $user): Builder
    {
        return $this->query()->where('user_id', $user->id);
    }

    public function itemsWithProducts(User $user): Collection
    {
        return $this->forUser($user)
            ->with(['product.category', 'product.coCheHoanPhi', 'parentProduct'])
            ->orderBy('id')
            ->get();
    }

    public function groupMode(User $user, int $parentId): ?string
    {
        return $this->forUser($user)->where('parent_product_id', $parentId)->value('mode');
    }

    public function lockItem(User $user, int $parentId, int $productId): ?CartItem
    {
        return $this->forUser($user)
            ->where('parent_product_id', $parentId)
            ->where('product_id', $productId)
            ->lockForUpdate()
            ->first();
    }

    public function findOfUser(User $user, int $itemId): ?CartItem
    {
        return $this->forUser($user)->with('parentProduct')->find($itemId);
    }

    public function clear(User $user, ?int $parentId = null): void
    {
        $this->forUser($user)
            ->when($parentId, fn ($q) => $q->where('parent_product_id', $parentId))
            ->delete();
    }

    public function removeProducts(User $user, int $parentId, array $productIds): void
    {
        $this->forUser($user)
            ->where('parent_product_id', $parentId)
            ->whereIn('product_id', $productIds)
            ->delete();
    }

    public function totalQuantity(User $user): int
    {
        return (int) $this->forUser($user)->sum('quantity');
    }
}
