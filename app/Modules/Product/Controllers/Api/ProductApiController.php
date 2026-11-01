<?php

namespace App\Modules\Product\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Product\Services\ProductApiService;
use App\Modules\Shared\Traits\PaginatedApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    use PaginatedApiResponse;

    public function __construct(private readonly ProductApiService $productApiService)
    {
    }

    public function categories(): JsonResponse
    {
        return $this->result($this->productApiService->categories());
    }

    public function products(Request $request): JsonResponse
    {
        return $this->paginatedResult($this->productApiService->products($request->all()));
    }

    public function categoryProducts(Request $request, int $category): JsonResponse
    {
        $payload = $this->productApiService->categoryProducts($category, $request->all());
        $products = $payload['products'];

        return $this->result(
            [
                'category' => $payload['category'],
                'mode' => $payload['mode'],
                'buy_mode' => $payload['buy_mode'],
                'products' => $products->items(),
            ],
            'Thanh cong',
            ['pagination' => $this->paginationPayload($products)],
            $this->paginationLinks($products)
        );
    }

    public function sanPhamTuChon(Request $request, int $product): JsonResponse
    {
        $payload = $this->productApiService->sanPhamTuChon($product, $request->all());

        if ($payload === null) {
            return response()->json([
                'success' => false,
                'message' => 'San pham nay khong thuoc co che mua tu chon.',
                'data'    => null,
                'meta'    => (object) [],
                'links'   => (object) [],
            ], 422);
        }

        $products = $payload['products'];

        return $this->result(
            [
                'product'         => $payload['product'],
                'co_che_hoan_phi' => $payload['co_che_hoan_phi'],
                'mode'            => $payload['mode'],
                'total'           => $products->total(),
                'products'        => $products->items(),
            ],
            'Thanh cong',
            ['pagination' => $this->paginationPayload($products)],
            $this->paginationLinks($products)
        );
    }

    public function productDetail(string $slug): JsonResponse
    {
        $payload = $this->productApiService->productDetail($slug);

        return $this->result($payload['data'], 'Thanh cong', ['related' => $payload['related']]);
    }

}
