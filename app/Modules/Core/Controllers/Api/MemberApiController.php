<?php

namespace App\Modules\Core\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Core\Services\MemberApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberApiController extends Controller
{
    public function __construct(private readonly MemberApiService $memberApiService)
    {
    }

    /**
     * GET /api/khach-hang-truc-tiep — khách hàng trực tiếp chưa mua hàng
     */
    public function khachHangTrucTiep(Request $request): JsonResponse
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $members = $this->memberApiService->khachHangTrucTiepChuaMua($request->user(), $request->only('search'));

        return response()->json([
            'success' => true,
            'message' => 'Thanh cong',
            'data'    => [
                'total'     => $members->total(),
                'customers' => $members->items(),
            ],
            'meta'    => [
                'pagination' => [
                    'current_page' => $members->currentPage(),
                    'per_page'     => $members->perPage(),
                    'total'        => $members->total(),
                    'last_page'    => $members->lastPage(),
                    'from'         => $members->firstItem(),
                    'to'           => $members->lastItem(),
                ],
            ],
            'links'   => [
                'first' => $members->url(1),
                'last'  => $members->url($members->lastPage()),
                'prev'  => $members->previousPageUrl(),
                'next'  => $members->nextPageUrl(),
            ],
        ]);
    }
}
