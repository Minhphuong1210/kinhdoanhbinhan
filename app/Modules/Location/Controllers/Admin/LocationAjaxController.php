<?php

namespace App\Modules\Location\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Location\Services\LocationService;
use Illuminate\Http\JsonResponse;

/**
 * AJAX địa chỉ hành chính cho form, không yêu cầu đăng nhập vì dùng ở trang đăng ký.
 */
class LocationAjaxController extends Controller
{
    public function __construct(private readonly LocationService $locations)
    {
    }

    public function districts(string $provinceId): JsonResponse
    {
        return response()->json($this->locations->districtsByCode($provinceId, ['id', 'name', 'prefix']));
    }

    public function wards(string $districtId): JsonResponse
    {
        return response()->json($this->locations->wardsByCode($districtId, ['id', 'name', 'prefix']));
    }

    public function pancakeDistricts(string $provinceId): JsonResponse
    {
        return response()->json($this->locations->pancakeDistricts($provinceId));
    }

    public function pancakeWards(string $districtId): JsonResponse
    {
        return response()->json($this->locations->pancakeCommunes($districtId));
    }

    public function bankCities(): JsonResponse
    {
        return response()->json($this->locations->bankAreas());
    }

    /** Chi nhánh theo thành phố */
    public function branches(string $cityId): JsonResponse
    {
        return response()->json($this->locations->branchesOfCity($cityId));
    }
}
