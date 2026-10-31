<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThanhPhoBanks extends Model
{
    protected $fillable = ['name'];

    public function nganHangs()
    {
        return $this->hasMany(NganHangBank::class, 'thanh_pho_id');
    }
}
