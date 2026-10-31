<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThuVien extends Model
{
    protected $table = 'thu_vien';
    protected $fillable = [
        'name','slug','intro','admin_id','content','image','status',
        'featured','meta_title','meta_keywords','meta_description','vi_tri_web',
    ];

    public function scopeForWeb($query)
    {
        return $query->where('vi_tri_web', config('app.vi_tri_web', 'cong_ty'));
    }
}
