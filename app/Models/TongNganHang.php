<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TongNganHang extends Model
{
    protected $table = 'tong_ngan_hang';
    protected $fillable = ['name','ten_viet_tat'];

}
