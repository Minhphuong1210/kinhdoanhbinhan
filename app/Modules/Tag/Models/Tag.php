<?php
namespace App\Modules\Tag\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name','slug','status'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'tag_products', 'tags_id', 'product_id');
    }
}
