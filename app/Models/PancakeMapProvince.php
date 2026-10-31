<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PancakeMapProvince extends Model
{
    protected $table = 'pancake_map_provinces';

    protected $fillable = [
        'province_id',
        'pancake_id',
        'pancake_new_id',
    ];
}