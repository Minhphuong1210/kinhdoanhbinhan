<?php

namespace App\Modules\Finance\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LichSuGiaoDich;
use App\Modules\Finance\Services\ActivityService;
use App\Modules\Shared\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityApiController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly ActivityService $activityService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['nullable', 'integer'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $items = $this->activityService->list($request->user()->id, $data);

        return $this->paginated($items, $items->getCollection()->map(fn (LichSuGiaoDich $item) => $this->activityService->payload($item)));
    }
}
