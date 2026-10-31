<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public const TARGET_USER = 'user';  // Gửi cho 1 user (target_id)
    public const TARGET_ALL  = 'all';   // Admin gửi cho tất cả mọi người
    public const TYPE_ALL  = 'tat_ca';
    public const TYPE_MUA_HANG  = 'mua_hang';
    public const TYPE_HOAN_PHI  = 'hoan_phi';
    public const VI_TRI_TAT_CA = 'tat_ca'; // Thông báo chung cho cả 2 app

    protected $table = 'notification';

    protected $fillable = ['target_type', 'title', 'image', 'content', 'target_id','order_id','vi_tri','type'];

    /**
     * Chỉ thông báo của website đang chạy (VI_TRI_WEB: cong_ty → cong_ty, htx → hop_tac_xa)
     * và thông báo chung cho cả 2 app.
     */
    public function scopeForCurrentSite($query)
    {
        return $query->whereIn('vi_tri', [FcmUser::defaultViTri(), self::VI_TRI_TAT_CA]);
    }

    /**
     * Thông báo riêng của user + thông báo gửi cho tất cả.
     */
    public function scopeVisibleTo($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where(function ($own) use ($user) {
                $own->where('target_type', self::TARGET_USER)->where('target_id', $user->id);
            })->orWhere('target_type', self::TARGET_ALL);
        });
    }

    public function scopeBroadcast($query)
    {
        return $query->where('target_type', self::TARGET_ALL);
    }

    public function isVisibleTo(User $user): bool
    {
        $isMine = $this->target_type === self::TARGET_ALL
            || ($this->target_type === self::TARGET_USER && (int) $this->target_id === (int) $user->id);

        return $isMine && in_array($this->vi_tri, [FcmUser::defaultViTri(), self::VI_TRI_TAT_CA], true);
    }

    public function reads()
    {
        return $this->hasMany(NotificationReads::class, 'notification_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'target_id');
    }
}
