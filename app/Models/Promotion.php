<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'name', 'month', 'year', 'is_active', 'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Lấy tất cả product_promotion của sự kiện này
     */
    public function productPromotions()
    {
        return $this->hasMany(ProductPromotion::class);
    }

    /**
     * Lấy sự kiện active của tháng/năm hiện tại
     */
    public static function activeThisMonth()
    {
        return static::where('month', now()->month)
            ->where('year', now()->year)
            ->where('is_active', 1)
            ->first();
    }

    /**
     * Tên hiển thị: "Tháng 2/2026"
     */
    public function getMonthLabelAttribute(): string
    {
        return "Tháng {$this->month}/{$this->year}";
    }
}
