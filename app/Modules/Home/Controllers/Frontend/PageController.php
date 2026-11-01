<?php

namespace App\Modules\Home\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Modules\Home\Services\PageService;

/**
 * Các trang tĩnh: giới thiệu, liên hệ, trung tâm hỗ trợ, chính sách.
 */
class PageController extends Controller
{
    public function __construct(private readonly PageService $pageService)
    {
    }

    public function about()
    {
        return view('Core::Frontend.pages.about', $this->pageService->aboutData());
    }

    public function contact()
    {
        return view('Core::Frontend.pages.contact');
    }

    public function support()
    {
        $policies = $this->pageService->policies();

        return view('Core::Frontend.pages.support', compact('policies'));
    }

    public function policy(string $slug)
    {
        $policies = $this->pageService->policies();
        $policy = $this->pageService->findPolicy($policies, $slug);

        abort_unless($policy, 404);

        return view('Core::Frontend.pages.policy', compact('policy', 'policies'));
    }
}
