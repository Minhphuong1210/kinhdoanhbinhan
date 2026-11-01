<?php

namespace App\Modules\Post\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Modules\Post\Services\NewsService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function __construct(private readonly NewsService $newsService)
    {
    }

    public function index(Request $request)
    {
        return view('Core::Frontend.news.index', $this->newsService->indexData(
            trim((string) $request->query('q')),
            $request->filled('slug') ? $request->query('slug') : null
        ));
    }

    public function show($slug)
    {
        return view('Core::Frontend.news.show', $this->newsService->detailData($slug));
    }
}
