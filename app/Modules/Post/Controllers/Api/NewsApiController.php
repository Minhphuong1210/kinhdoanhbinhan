<?php

namespace App\Modules\Post\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Post\Services\PostApiService;
use App\Modules\Shared\Traits\PaginatedApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsApiController extends Controller
{
    use PaginatedApiResponse;

    public function __construct(private readonly PostApiService $postApiService)
    {
    }

    public function newsCategories(): JsonResponse
    {
        return $this->result($this->postApiService->newsCategories());
    }

    public function news(Request $request): JsonResponse
    {
        return $this->paginatedResult($this->postApiService->news($request->all()));
    }

    public function newsDetail(string $slug): JsonResponse
    {
        $payload = $this->postApiService->newsDetail($slug);

        return $this->result($payload['data'], 'Thanh cong', ['related' => $payload['related']]);
    }
}
