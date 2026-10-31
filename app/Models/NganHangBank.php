<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NganHangBank extends Model
{
    protected $table = 'ngan_hangs';
    protected $fillable = ['name','thanh_pho_id','tong_ngan_hang_id'];
    public function thanhPho()
    {
        return $this->belongsTo(ThanhPhoBanks::class, 'thanh_pho_id');
    }

    // ngân hàng có nhiều yêu cầu rút
    public function yeuCauRutTiens()
    {
        return $this->hasMany(YeuCauRutTien::class, 'ngan_hang_id');
    }

}
