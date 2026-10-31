<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichSuGiaoDich extends Model
{
    protected $table = 'lich_su_giao_dich';
    protected $fillable = [
        'sender_id','receiver_id','amount','note','type',
        'admin_id','surplus','hoan_f1','company_id','product_id','surplus_before','surplus_vi_mua','surplus_vi_mua_befor'
    ];

    // Người nhận (admin_id = người sở hữu giao dịch / nhận hoa hồng)
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'admin_id');
    }

    // Người gửi / người mua (sender_id)
    public function sender()
    {
        return $this->belongsTo(\App\Models\User::class, 'sender_id');
    }

    // Người nhận (receiver_id — dùng khi có chuyển điểm trực tiếp)
    public function receiver()
    {
        return $this->belongsTo(\App\Models\User::class, 'receiver_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }
}
