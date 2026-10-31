<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YeuCauRutTien extends Model
{
    protected $table = 'yeu_cau_rut_tien';
    protected $fillable = ['user_id','so_tien','cast_now','stk','ngan_hang','chu_tk','status','note','so_tien_sau_thue','cccd','ngan_hang_id','10_phan_tram_so_tien','1_5_phan_tram_so_tien'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function nganHang()
    {
        return $this->belongsTo(NganHangBank::class, 'ngan_hang_id');
    }

}
