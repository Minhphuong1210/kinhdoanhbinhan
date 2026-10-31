<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Districts extends Model
{
    protected $table = 'districts';
    protected $fillable = ['name','prefix','province_id'];

   
}
