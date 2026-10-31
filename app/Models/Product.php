<?php
namespace App\Models;

use App\Modules\Tag\Models\Tag;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'price', 'purchase_price', 'old_price', 'price_hoan_tien',
        'category_id', 'image', 'image_extra', 'video', 'description', 'content',
        'active', 'slug', 'view', 'hien_trang_chu', 'san_pham_noi_bat',
        'san_pham_hien_nhu_baner', 'parent_id', 'san_pham_hoan', 'co_che_hoan_phi_id', 'stt',
        'instructions', 'licenses', 'origin', 'quantity', 'product_pancake_id', 'variation_pancake_id'
    ];

    protected $casts = [
        'image_extra' => 'array',
        'licenses' => 'array',
        'quantity' => 'integer',
    ];

    /**
     * Sản phẩm được chọn khi mua tự chọn theo cơ chế (trang_hoat_dong = tu_chon):
     * đang bán, còn hàng, cùng co_che_hoan_phi_id.
     */
    public function scopeSelectableInCoChe($query, int $coCheHoanPhiId)
    {
        return $query->where('active', 1)
            ->availableForPurchase()
            ->where('co_che_hoan_phi_id', $coCheHoanPhiId);
    }

    public function scopeAvailableForPurchase($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('quantity')
                ->orWhere('quantity', '>', 0);
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_products', 'product_id', 'tags_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function coCheHoanPhi()
    {
        return $this->belongsTo(CoCheHoanPhi::class, 'co_che_hoan_phi_id');
    }

    public function hasChildren(): bool
    {
        return $this->children()->where('active', 1)->availableForPurchase()->exists();
    }

    public function getActiveChildren()
    {
        return $this->children()->where('active', 1)->availableForPurchase()->orderBy('stt', 'asc')->get();
    }
}
