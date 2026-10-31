<?php

namespace App\Modules\Core\Traits;

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
