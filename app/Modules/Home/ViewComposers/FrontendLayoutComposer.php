<?php

namespace App\Modules\Home\ViewComposers;

use App\Modules\Category\Models\Category;
use App\Modules\ChinhSach\Models\ChinhSach;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Dữ liệu dùng chung cho layout frontend: thông tin doanh nghiệp (chân trang theo yêu cầu
 * Bộ Công Thương), menu danh mục sản phẩm, danh sách chính sách.
 */
class FrontendLayoutComposer
{
    /**
     * Chỉ những setting được phép hiện ra ngoài website.
     * Bảng setting còn chứa mật khẩu mail, API key... — không bao giờ đưa cả bảng ra view.
     */
    public const PUBLIC_KEYS = [
        'name', 'logo', 'favicon', 'hotline', 'email', 'address',
        'ma_so_doanh_nghiep', 'GCNDKDN', 'nguoi_dai_dien', 'don_vi_phan_phoi',
        'google_map', 'facebook', 'zalo', 'messenger', 'youtube', 'instagram', 'tiktok', 'fanpage',
        'copyright', 'default_meta_title', 'default_meta_description', 'default_meta_keywords',
        'link_bo_cong_thuong',
        'video_kinhdoanhbinhan', 'video_hoptacxa', 'video_gioi_thieu_ve_chung_toi',
    ];

    public const CACHE_KEY = 'frontend_layout_data_';

    /** Dữ liệu đã lấy trong request hiện tại (layout + trang con cùng dùng) */
    private static ?array $resolved = null;

    public function compose(View $view): void
    {
        self::$resolved ??= Cache::remember(self::CACHE_KEY . config('app.vi_tri_web'), 300, fn () => $this->data());

        $view->with(self::$resolved);
    }

    public static function forget(): void
    {
        self::$resolved = null;
        Cache::forget(self::CACHE_KEY . config('app.vi_tri_web'));
    }

    private function data(): array
    {
        // Có key bị trùng (vd facebook = '#' ở social_tab và link thật ở general_tab):
        // lấy theo id tăng dần, giá trị thật ghi sau sẽ đè, rồi bỏ các giá trị '#'
        $site = Setting::forWeb()
            ->whereIn('name', self::PUBLIC_KEYS)
            ->orderBy('id')
            ->get(['name', 'value'])
            ->reduce(function (array $carry, Setting $s) {
                $value = trim((string) $s->value);
                if ($value !== '' && $value !== '#') {
                    $carry[$s->name] = $value;
                }

                return $carry;
            }, []);

        $site['logo_url']    = !empty($site['logo']) ? cdn($site['logo']) : asset('logo/logo.png');
        $site['favicon_url'] = !empty($site['favicon']) ? cdn($site['favicon']) : asset('favicon.ico');

        $menuCategories = Category::where('active', 1)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $policies = ChinhSach::forWeb()
            ->where('status', 1)
            ->orderBy('id')
            ->get(['id', 'title', 'url']);

        return [
            'site'           => $site,
            'menuCategories' => $menuCategories,
            'footerPolicies' => $policies,
        ];
    }
}
