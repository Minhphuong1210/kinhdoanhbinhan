<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'name','slug','image','description','content','view',
        'category_post_id','admin_id','status','featured',
        'meta_title','meta_keywords','meta_description','vi_tri_web',
    ];

    public function category()
    {
        return $this->belongsTo(CategoryPost::class, 'category_post_id');
    }

    public function scopeForWeb($query)
    {
        return $query->where('vi_tri_web', config('app.vi_tri_web', 'cong_ty'));
    }
}
