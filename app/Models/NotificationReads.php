<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationReads extends Model
{
    protected $table = 'notification_reads';

    protected $fillable = ['notification_id', 'user_id', 'read_at'];

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
