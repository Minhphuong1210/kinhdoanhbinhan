<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPromotion extends Model
{
    protected $table = 'product_promotion';

    protected $fillable = [
        'promotion_id', 'product_id',
    ];

    /**
     * Sự kiện khuyến mãi
     */
    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    /**
     * Sản phẩm chính
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Danh sách quà tặng / khuyến mãi
     */
    public function gifts()
    {
        return $this->hasMany(Gift::class);
    }
}
