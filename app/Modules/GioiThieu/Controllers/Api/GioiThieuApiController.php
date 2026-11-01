<?php

namespace App\Modules\GioiThieu\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\GioiThieu\Services\GioiThieuApiService;
use App\Modules\Shared\Traits\PaginatedApiResponse;
use Illuminate\Http\JsonResponse;

class GioiThieuApiController extends Controller
{
    use PaginatedApiResponse;

    public function __construct(private readonly GioiThieuApiService $gioiThieuService)
    {
    }

    public function gioiThieuChungToi(): JsonResponse
    {
        return $this->result($this->gioiThieuService->gioiThieuChungToi()['data']);
    }
}
