<?php

namespace App\Modules\Product\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Product\Services\ProductApiService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function __construct(private readonly ProductApiService $productApiService)
    {
    }

    public function categories(): JsonResponse
    {
        return $this->success($this->productApiService->categories());
    }

    public function products(Request $request): JsonResponse
    {
        return $this->paginated($this->productApiService->products($request->all()));
    }

    public function categoryProducts(Request $request, int $category): JsonResponse
    {
        $payload = $this->productApiService->categoryProducts($category, $request->all());
        $products = $payload['products'];

        return $this->success(
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

        return $this->success(
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

        return $this->success($payload['data'], 'Thanh cong', ['related' => $payload['related']]);
    }

    public function newsCategories(): JsonResponse
    {
        return $this->success($this->productApiService->newsCategories());
    }

    public function news(Request $request): JsonResponse
    {
        return $this->paginated($this->productApiService->news($request->all()));
    }

    public function newsDetail(string $slug): JsonResponse
    {
        $payload = $this->productApiService->newsDetail($slug);

        return $this->success($payload['data'], 'Thanh cong', ['related' => $payload['related']]);
    }


public function gioiThieuChungToi(){
    $payload = $this->productApiService->gioiThieuChungToi();

    return $this->success($payload['data'], 'Thanh cong',);

}

    private function success($data = null, string $message = 'Thanh cong', array $meta = [], array $links = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => empty($meta) ? (object) [] : $meta,
            'links' => empty($links) ? (object) [] : $links,
        ], $status);
    }

    private function paginated(LengthAwarePaginator $paginator): JsonResponse
    {
        return $this->success(
            $paginator->items(),
            'Thanh cong',
            [
                'pagination' => [
                    ...$this->paginationPayload($paginator),
                ],
            ],
            $this->paginationLinks($paginator)
        );
    }

    private function paginationPayload(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ];
    }

    private function paginationLinks(LengthAwarePaginator $paginator): array
    {
        return [
            'first' => $paginator->url(1),
            'last' => $paginator->url($paginator->lastPage()),
            'prev' => $paginator->previousPageUrl(),
            'next' => $paginator->nextPageUrl(),
        ];
    }
}
