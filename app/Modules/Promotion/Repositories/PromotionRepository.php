<?php

namespace App\Modules\Promotion\Repositories;

use App\Models\Gift;
use App\Models\ProductPromotion;
use App\Models\Promotion;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PromotionRepository extends BaseRepository
{
    protected string $model = Promotion::class;

    public function paginateForAdmin(): LengthAwarePaginator
    {
        return $this->query()
            ->withCount('productPromotions')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate(15);
    }

    public function hasProduct(Promotion $promotion, int|string $productId): bool
    {
        return ProductPromotion::where('promotion_id', $promotion->id)
            ->where('product_id', $productId)
            ->exists();
    }

    public function addProduct(Promotion $promotion, int|string $productId): ProductPromotion
    {
        return ProductPromotion::create([
            'promotion_id' => $promotion->id,
            'product_id'   => $productId,
        ]);
    }

    public function addGift(array $data): Gift
    {
        return Gift::create($data);
    }
}
