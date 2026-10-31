<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoCheHoanPhi extends Model
{
    protected $table = 'co_che_hoan_phi';

    protected $fillable = [
        'name',
        'tang_1',
        'tang_2',
        'tang_3',
        'tang_4',
        'tang_5',
        'tang_6',
        'parent_id',
        'trang_hoat_dong',
    ];

    protected $casts = [
        'tang_1' => 'float',
        'tang_2' => 'float',
        'tang_3' => 'float',
        'tang_4' => 'float',
        'tang_5' => 'float',
        'tang_6' => 'float',
        'parent_id' => 'integer',
    ];

    public function getRates(): array
    {
        return [
            1 => (float)$this->tang_1,
            2 => (float)$this->tang_2,
            3 => (float)$this->tang_3,
            4 => (float)$this->tang_4,
            5 => (float)$this->tang_5,
            6 => (float)$this->tang_6,
        ];
    }

    public function parent()
    {
        return $this->belongsTo(CoCheHoanPhi::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CoCheHoanPhi::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'co_che_hoan_phi_id');
    }
}
