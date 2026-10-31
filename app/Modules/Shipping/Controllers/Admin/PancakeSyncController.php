<?php

namespace App\Modules\Shipping\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Shipping\Models\ShippingCarrier;
use App\Modules\Shipping\Services\PancakeSyncService;
use Illuminate\Http\JsonResponse;

class PancakeSyncController extends Controller
{
    public function __construct(private readonly PancakeSyncService $syncService)
    {
    }

    public function shops(): JsonResponse
    {
        return $this->withCarrier(fn ($carrier) => $this->syncService->shops($carrier));
    }

    public function warehouses(): JsonResponse
    {
        return $this->withCarrier(fn ($carrier) => $this->syncService->warehouses($carrier));
    }

    public function products(): JsonResponse
    {
        return $this->withCarrier(fn ($carrier) => $this->syncService->syncProducts($carrier));
    }

    public function provinces(): JsonResponse
    {
        return $this->withCarrier(fn ($carrier) => [
            'success' => true,
            'total'   => $this->syncService->syncProvinces($carrier),
        ]);
    }

    public function districts(): JsonResponse
    {
        return $this->withCarrier(function ($carrier) {
            $this->syncService->syncDistricts($carrier);

            return ['success' => true];
        });
    }

    public function communes(): JsonResponse
    {
        return $this->withCarrier(fn ($carrier) => ['success' => true] + $this->syncService->syncCommunes($carrier));
    }

    public function mapProvinces(): JsonResponse
    {
        return response()->json($this->syncService->mapProvinces());
    }

    private function withCarrier(callable $action): JsonResponse
    {
        $carrier = $this->syncService->carrier();

        if (!$carrier instanceof ShippingCarrier) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy cấu hình Pancake']);
        }

        try {
            return response()->json($action($carrier));
        } catch (\Exception $e) {
            report($e);

            return response()->json(['success' => false, 'message' => 'Không kết nối được Pancake'], 500);
        }
    }
}
