<?php

namespace App\Modules\Shared\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Format response chung cho API: { success, message, data, meta, links }.
 */
trait ApiResponse
{
    protected function success($data = null, string $message = 'Thanh cong', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'meta'    => (object) [],
            'links'   => (object) [],
        ], $status);
    }

    /** Danh sách có phân trang, $items là dữ liệu đã chuyển sang payload */
    protected function paginated($paginator, $items): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Thanh cong',
            'data'    => $items,
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'from'         => $paginator->firstItem(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'to'           => $paginator->lastItem(),
                'total'        => $paginator->total(),
            ],
            'links'   => [
                'first' => $paginator->url(1),
                'last'  => $paginator->url($paginator->lastPage()),
                'prev'  => $paginator->previousPageUrl(),
                'next'  => $paginator->nextPageUrl(),
            ],
        ]);
    }

    protected function error(string $message, int $status = 422, $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => $data,
            'meta'    => (object) [],
            'links'   => (object) [],
        ], $status);
    }
}
