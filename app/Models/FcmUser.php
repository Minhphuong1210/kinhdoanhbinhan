<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FcmUser extends Model
{
    public const VI_TRI_CONG_TY    = 'cong_ty';     // Thông báo mua hàng
    public const VI_TRI_HOP_TAC_XA = 'hop_tac_xa';  // Thông báo hoàn phí
    public const VI_TRIS           = [self::VI_TRI_CONG_TY, self::VI_TRI_HOP_TAC_XA];

    protected $table = 'fcm_user';

    protected $fillable = ['user_id', 'fcm', 'vi_tri', 'device_name', 'last_used_at'];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * vi_tri mặc định theo site đang chạy (config app.vi_tri_web: cong_ty | htx).
     */
    public static function defaultViTri(): string
    {
        return config('app.vi_tri_web') === 'htx' ? self::VI_TRI_HOP_TAC_XA : self::VI_TRI_CONG_TY;
    }
}
