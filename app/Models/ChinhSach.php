<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChinhSach extends Model
{
    protected $table = 'chinh_sach';
    protected $fillable = ['title','content','url','image','status','vi_tri_web'];

    protected static function booted(): void
    {
        // Chân trang website hiển thị danh sách chính sách → xoá cache khi sửa
        static::saved(fn () => \App\Modules\Core\ViewComposers\FrontendLayoutComposer::forget());
        static::deleted(fn () => \App\Modules\Core\ViewComposers\FrontendLayoutComposer::forget());
    }

    /** Slug trên URL: dùng cột url, chưa có thì sinh từ tiêu đề */
    public function getSlugAttribute(): string
    {
        return $this->url ?: \Illuminate\Support\Str::slug($this->title);
    }

    /**
     * Nội dung với biến {ten_cong_ty}, {hotline}... thay bằng thông tin ở Cài đặt.
     */
    public function renderContent(array $site): string
    {
        return strtr((string) $this->content, [
            '{ten_cong_ty}'        => e($site['name'] ?? ''),
            '{ma_so_doanh_nghiep}' => e($site['ma_so_doanh_nghiep'] ?? ''),
            '{dia_chi}'            => e($site['address'] ?? ''),
            '{hotline}'            => e($site['hotline'] ?? ''),
            '{email}'              => e($site['email'] ?? ''),
            '{website}'            => e(request()->getHost()),
        ]);
    }

    public function scopeForWeb($query)
    {
        return $query->where('vi_tri_web', config('app.vi_tri_web', 'cong_ty'));
    }
}
