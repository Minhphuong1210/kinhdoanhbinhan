<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PancakeProvinces extends Model
{
    protected $table = 'pancake_provinces';
    protected $fillable = ['country_code','id_pancake','name','name_en','new_id','region_type'];


}
