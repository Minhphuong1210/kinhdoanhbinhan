<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /** Setting dạng video (upload mp4/webm/ogg ở trang Cài đặt) */
    public const VIDEO_NAMES = [
        'video_kinhdoanhbinhan',
        'video_hoptacxa',
        'video_gioi_thieu_ve_chung_toi',
    ];

    protected $table = 'setting';

    /** Setting chứa mật khẩu / key: không được trả giá trị ra trình duyệt */
    public const SECRET_NAMES = ['password', 'mailgun_secret', 'mailgun_password', 'smtp_password', 'fpt_api_key'];

    public function isSecret(): bool
    {
        return in_array($this->name, self::SECRET_NAMES, true);
    }

    public function isVideo(): bool
    {
        return in_array($this->name, self::VIDEO_NAMES, true) || $this->type === 'video';
    }
    protected $fillable = ['name','value','type','vi_tri_web'];

    public function scopeForWeb($query)
    {
        return $query->where('vi_tri_web', config('app.vi_tri_web', 'cong_ty'));
    }
}
