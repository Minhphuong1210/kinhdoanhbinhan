<?php

namespace App\Modules\Home\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Modules\Home\Services\HomeService;

class HomeController extends Controller
{
    public function __construct(private readonly HomeService $homeService)
    {
    }

    public function index()
    {
        // Thông tin doanh nghiệp lấy qua FrontendLayoutComposer (chỉ các key công khai)
        return view('Core::Frontend.home.index', $this->homeService->data());
    }
}
