<?php

namespace App\Modules\Shared\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

/**
 * Format API danh mục: meta.pagination + links, dùng cho sản phẩm và tin tức.
 */
trait PaginatedApiResponse
{
    protected function result($data = null, string $message = 'Thanh cong', array $meta = [], array $links = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => empty($meta) ? (object) [] : $meta,
            'links' => empty($links) ? (object) [] : $links,
        ], $status);
    }

    protected function paginatedResult(LengthAwarePaginator $paginator): JsonResponse
    {
        return $this->result(
            $paginator->items(),
            'Thanh cong',
            ['pagination' => $this->paginationPayload($paginator)],
            $this->paginationLinks($paginator)
        );
    }

    protected function paginationPayload(LengthAwarePaginator $paginator): array
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

    protected function paginationLinks(LengthAwarePaginator $paginator): array
    {
        return [
            'first' => $paginator->url(1),
            'last' => $paginator->url($paginator->lastPage()),
            'prev' => $paginator->previousPageUrl(),
            'next' => $paginator->nextPageUrl(),
        ];
    }
}
