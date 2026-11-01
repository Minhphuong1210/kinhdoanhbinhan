<?php

namespace App\Modules\Product\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Modules\Product\Services\ProductCatalogService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private readonly ProductCatalogService $catalog)
    {
    }

    public function index(Request $request)
    {
        return view('Core::Frontend.products.index', $this->catalog->websiteIndexData(
            trim((string) $request->query('q')),
            $request->query('sort'),
            $request->filled('category') ? $request->query('category') : null
        ));
    }

    public function show($slug)
    {
        return view('Core::Frontend.products.show', $this->catalog->websiteDetailData($slug));
    }
}
