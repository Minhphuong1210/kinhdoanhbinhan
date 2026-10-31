<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PancakeCommunes extends Model
{
    protected $table = 'pancake_communes';

    protected $fillable = [
        'id_pancake',
        'province_id',
        'district_id',
        'name',
        'name_en',
        'new_id',
        'postcode',
    ];
}