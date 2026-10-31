<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'name','ma_don_hang','address','user_id','status','note',
        'receive_user','receive_address','receive_phone',
        'total_price','total_price_hoan_tien','first_order',
        'province_id','district_id','ward_id','shipping_price','price_vat','the_loai',
        'device','thoi_gian_mua'
    ];

    protected $casts = [
        'thoi_gian_mua' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function province()
    {
        return $this->belongsTo(Provinces::class, 'province_id');
    }

    public function district()
    {
        return $this->belongsTo(Districts::class, 'district_id');
    }

    public function ward()
    {
        return $this->belongsTo(Wards::class, 'ward_id');
    }

    public function pancakeProvince()
    {
        return $this->belongsTo(
            PancakeProvinces::class,
            'province_id', // orders.province_id
            'id_pancake'   // pancake_provinces.id_pancake
        );
    }

    public function pancakeDistrict()
    {
        return $this->belongsTo(
            PancakeDistricts::class,
            'district_id',
            'id_pancake'
        );
    }

    public function pancakeWard()
    {
        return $this->belongsTo(
            PancakeCommunes::class,
            'ward_id',
            'id_pancake'
        );
    }
}
