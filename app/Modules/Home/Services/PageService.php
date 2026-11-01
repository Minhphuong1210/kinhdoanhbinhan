<?php

namespace App\Modules\Home\Services;

use App\Modules\ChinhSach\Models\ChinhSach;
use App\Modules\GioiThieu\Models\GioiThieu;
use App\Modules\Leader\Models\Leader;
use Illuminate\Support\Collection;

/**
 * Các trang tĩnh: giới thiệu, trung tâm hỗ trợ, chính sách.
 */
class PageService
{
    public function aboutData(): array
    {
        return [
            'about'   => GioiThieu::forWeb()->where('vi_tri', 've_chung_toi')->first() ?? GioiThieu::forWeb()->first(),
            'stories' => GioiThieu::forWeb()->where('vi_tri', 'like', 'gioi_thieu_%')->orderBy('vi_tri')->get(),
            'leaders' => Leader::where('status', 1)->orderByDesc('order_no')->limit(8)->get(),
        ];
    }

    public function policies(): Collection
    {
        return ChinhSach::forWeb()->where('status', 1)->orderBy('id')->get();
    }

    /** Ưu tiên khớp cột url, không có thì so slug sinh từ tiêu đề */
    public function findPolicy(Collection $policies, string $slug): ?ChinhSach
    {
        return $policies->firstWhere('url', $slug)
            ?? $policies->first(fn (ChinhSach $p) => $p->slug === $slug);
    }
}
