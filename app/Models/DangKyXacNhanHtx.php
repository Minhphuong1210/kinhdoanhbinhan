<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DangKyXacNhanHtx extends Model
{
    protected $table = 'dang_ky_xac_nhan_htx';
    protected $fillable = [
        'admin_id','image','status','full_name','birthday','gender','nationality',
        'identity_number','identity_date','identity_place','permanent_address',
        'current_address','phone','email','job','workplace','area','referrer',
        'know_from','know_from_other','goals','goal_other','cccd_files','portrait',
        'voluntary','understand_model','comply_rules','correct_communication',
        'foundation_lesson','data_consent','notify_channels','note',
    ];



    public function user()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

}
