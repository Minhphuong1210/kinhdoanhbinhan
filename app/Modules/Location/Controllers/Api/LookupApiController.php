<?php

namespace App\Modules\Location\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Location\Services\LocationService;
use App\Modules\Shared\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LookupApiController extends Controller
{
    use ApiResponse;

    private const PROVINCE_COLUMNS = ['id', 'name', 'type'];
    private const BANK_COLUMNS = ['id', 'name', 'ten_viet_tat'];
    private const DISTRICT_COLUMNS = ['id', 'name', 'prefix', 'province_id'];
    private const WARD_COLUMNS = ['id', 'name', 'prefix', 'district_id'];

    public function __construct(private readonly LocationService $locations)
    {
    }

    public function registerOptions(Request $request): JsonResponse
    {
        $data = $request->validate([
            'province_id' => ['nullable', 'exists:provinces,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'bank_area_id' => ['nullable', 'exists:thanh_pho_banks,id'],
            'tong_ngan_hang_id' => ['nullable', 'exists:tong_ngan_hang,id'],
        ]);

        return $this->success([
            'provinces' => $this->locations->provinces(self::PROVINCE_COLUMNS),
            'bank_areas' => $this->locations->bankAreas(),
            'banks' => $this->locations->banks(self::BANK_COLUMNS),
            'districts' => !empty($data['province_id'])
                ? $this->locations->districtsByCode($data['province_id'], self::DISTRICT_COLUMNS)
                : [],
            'wards' => !empty($data['district_id'])
                ? $this->locations->wardsByCode($data['district_id'], self::WARD_COLUMNS)
                : [],
            'bank_branches' => $this->locations->branchesByFilter($data),
        ]);
    }

    public function provinces(): JsonResponse
    {
        return $this->success($this->locations->provinces(self::PROVINCE_COLUMNS));
    }

    public function districts(Request $request): JsonResponse
    {
        $data = $request->validate([
            'province_id' => ['required', 'exists:provinces,id'],
        ]);

        return $this->success($this->locations->districtsByCode($data['province_id'], self::DISTRICT_COLUMNS));
    }

    public function wards(Request $request): JsonResponse
    {
        $data = $request->validate([
            'district_id' => ['required', 'exists:districts,id'],
        ]);

        return $this->success($this->locations->wardsByCode($data['district_id'], self::WARD_COLUMNS));
    }

    public function bankAreas(): JsonResponse
    {
        return $this->success($this->locations->bankAreas());
    }

    public function banks(): JsonResponse
    {
        return $this->success($this->locations->banks(self::BANK_COLUMNS));
    }

    public function bankBranches(Request $request): JsonResponse
    {
        $data = $request->validate([
            'bank_area_id' => ['nullable', 'exists:thanh_pho_banks,id'],
            'tong_ngan_hang_id' => ['nullable', 'exists:tong_ngan_hang,id'],
        ]);

        return $this->success($this->locations->branchesByFilter($data));
    }
}
