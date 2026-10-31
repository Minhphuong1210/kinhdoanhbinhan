<?php

namespace App\Modules\Core\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ChinhSach;
use App\Models\GioiThieu;
use App\Models\Leader;

/**
 * Các trang tĩnh: giới thiệu, liên hệ, trung tâm hỗ trợ, chính sách.
 */
class PageController extends Controller
{
    public function about()
    {
        $about = GioiThieu::forWeb()->where('vi_tri', 've_chung_toi')->first()
            ?? GioiThieu::forWeb()->first();

        $stories = GioiThieu::forWeb()
            ->where('vi_tri', 'like', 'gioi_thieu_%')
            ->orderBy('vi_tri')
            ->get();

        $leaders = Leader::where('status', 1)->orderByDesc('order_no')->limit(8)->get();

        return view('Core::Frontend.pages.about', compact('about', 'stories', 'leaders'));
    }

    public function contact()
    {
        return view('Core::Frontend.pages.contact');
    }

    public function support()
    {
        $policies = ChinhSach::forWeb()->where('status', 1)->orderBy('id')->get();

        return view('Core::Frontend.pages.support', compact('policies'));
    }

    public function policy(string $slug)
    {
        $policies = ChinhSach::forWeb()->where('status', 1)->orderBy('id')->get();

        // Ưu tiên khớp cột url, không có thì so slug sinh từ tiêu đề
        $policy = $policies->firstWhere('url', $slug)
            ?? $policies->first(fn (ChinhSach $p) => $p->slug === $slug);

        abort_unless($policy, 404);

        return view('Core::Frontend.pages.policy', compact('policy', 'policies'));
    }
}
