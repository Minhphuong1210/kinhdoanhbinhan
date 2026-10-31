<?php

namespace App\Modules\Promotion\Services;

use App\Models\Gift;
use App\Models\ProductPromotion;
use App\Models\Promotion;
use App\Modules\Promotion\Repositories\PromotionRepository;
use App\Modules\Shared\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class PromotionService extends BaseService
{
    public function __construct(PromotionRepository $repository)
    {
        parent::__construct($repository);
    }

    public function list(): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin();
    }

    public function create(array $data): Model
    {
        if (array_key_exists('is_active', $data)) {
            $data['is_active'] = 1;
        }

        return parent::create($data);
    }

    public function update(Model $model, array $data): Model
    {
        // Sửa: bỏ tick is_active nghĩa là tắt sự kiện
        $data['is_active'] = array_key_exists('is_active', $data) ? 1 : 0;

        return parent::update($model, $data);
    }

    public function loadForEdit(Promotion $promotion): Promotion
    {
        return $promotion->load('productPromotions.product', 'productPromotions.gifts.giftProduct');
    }

    /** @return bool false nếu sản phẩm đã có trong sự kiện */
    public function addProduct(Promotion $promotion, int|string $productId): bool
    {
        if ($this->repository->hasProduct($promotion, $productId)) {
            return false;
        }

        $this->repository->addProduct($promotion, $productId);

        return true;
    }

    public function addGift(ProductPromotion $productPromotion, array $data): Gift
    {
        $gift = [
            'product_promotion_id' => $productPromotion->id,
            'type'                 => $data['type'],
        ];

        if ($data['type'] === Gift::TYPE_PRODUCT) {
            $gift['gift_product_id'] = $data['gift_product_id'];
            $gift['quantity'] = $data['quantity'];
        } else {
            $gift['discount_value'] = $data['discount_value'];
        }

        return $this->repository->addGift($gift);
    }
}
