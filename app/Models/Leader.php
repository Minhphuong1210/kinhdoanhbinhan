<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leader extends Model
{
    protected $table = 'leaders';
    protected $fillable = [
        'name','image','chucvu','ngaysinh','gender','profile','ambassador','phone',
        'hoc_van','advantages','progress','years','slogan','achievements',
        'contributions','goals','extra_profile','intro','status','order_no',
        'dedication','is_ambassador',
    ];
}
