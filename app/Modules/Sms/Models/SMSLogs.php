<?php
namespace App\Modules\Sms\Models;

use Illuminate\Database\Eloquent\Model;

class SMSLogs extends Model
{
    protected $table = 'sms_logs';
    protected $fillable = ['phone','content','brandname','request_id','status','response'];
}
