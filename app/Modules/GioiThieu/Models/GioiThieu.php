<?php
namespace App\Modules\GioiThieu\Models;

use Illuminate\Database\Eloquent\Model;

class GioiThieu extends Model
{
    protected $table = 'gioi_thieu';
    protected $fillable = ['name','image','content','vi_tri','year','vi_tri_web','nam_kinh_doanh','khach_hang','san_pham'];

    public function scopeForWeb($query)
    {
        return $query->where('vi_tri_web', config('app.vi_tri_web', 'cong_ty'));
    }
}
