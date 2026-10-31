<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    protected $fillable = [
        'product_promotion_id', 'type',
        'gift_product_id', 'quantity',
        'discount_value',
    ];

    // Loại khuyến mãi
    const TYPE_PRODUCT          = 'product';           // Tặng sản phẩm
    const TYPE_DISCOUNT_PERCENT = 'discount_percent';  // Giảm giá %
    const TYPE_DISCOUNT_AMOUNT  = 'discount_amount';   // Trừ tiền cố định

    /**
     * ProductPromotion cha
     */
    public function productPromotion()
    {
        return $this->belongsTo(ProductPromotion::class);
    }

    /**
     * Sản phẩm quà tặng (chỉ dùng khi type = 'product')
     */
    public function giftProduct()
    {
        return $this->belongsTo(Product::class, 'gift_product_id');
    }

    /**
     * Kiểm tra loại
     */
    public function isProductGift(): bool
    {
        return $this->type === self::TYPE_PRODUCT;
    }

    public function isDiscountPercent(): bool
    {
        return $this->type === self::TYPE_DISCOUNT_PERCENT;
    }

    public function isDiscountAmount(): bool
    {
        return $this->type === self::TYPE_DISCOUNT_AMOUNT;
    }

    /**
     * Mô tả hiển thị
     */
    public function getDescriptionAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_PRODUCT => 'Tặng ' . ($this->giftProduct->name ?? '?') . ' x' . $this->quantity,
            self::TYPE_DISCOUNT_PERCENT => 'Giảm ' . $this->discount_value . '%',
            self::TYPE_DISCOUNT_AMOUNT => 'Giảm ' . number_format($this->discount_value, 0, ',', '.') . '₫',
            default => '—',
        };
    }
}
