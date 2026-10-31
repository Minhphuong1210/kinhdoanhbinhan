<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PancakeDistricts extends Model
{
    protected $table = 'pancake_districts';

    protected $fillable = [
        'id_pancake',
        'province_id',
        'name',
        'name_en',
    ];
}