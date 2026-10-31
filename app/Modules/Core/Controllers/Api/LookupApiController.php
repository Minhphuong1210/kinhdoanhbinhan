<?php

namespace App\Modules\Core\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Districts;
use App\Models\NganHangBank;
use App\Models\Provinces;
use App\Models\ThanhPhoBanks;
use App\Models\TongNganHang;
use App\Models\Wards;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LookupApiController extends Controller
{
    public function registerOptions(Request $request): JsonResponse
    {
        $data = $request->validate([
            'province_id' => ['nullable', 'exists:provinces,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'bank_area_id' => ['nullable', 'exists:thanh_pho_banks,id'],
            'tong_ngan_hang_id' => ['nullable', 'exists:tong_ngan_hang,id'],
        ]);

        return $this->success([
            'provinces' => $this->provinceQuery()->get(),
            'bank_areas' => $this->bankAreaQuery()->get(),
            'banks' => $this->bankQuery()->get(),
            'districts' => !empty($data['province_id'])
                ? $this->districtsByProvince($data['province_id'])
                : [],
            'wards' => !empty($data['district_id'])
                ? $this->wardsByDistrict($data['district_id'])
                : [],
            'bank_branches' => $this->bankBranchesByFilter($data),
        ]);
    }

    public function provinces(): JsonResponse
    {
        return $this->success($this->provinceQuery()->get());
    }

    public function districts(Request $request): JsonResponse
    {
        $data = $request->validate([
            'province_id' => ['required', 'exists:provinces,id'],
        ]);

        return $this->success($this->districtsByProvince($data['province_id']));
    }

    public function wards(Request $request): JsonResponse
    {
        $data = $request->validate([
            'district_id' => ['required', 'exists:districts,id'],
        ]);

        return $this->success($this->wardsByDistrict($data['district_id']));
    }

    public function bankAreas(): JsonResponse
    {
        return $this->success($this->bankAreaQuery()->get());
    }

    public function banks(): JsonResponse
    {
        return $this->success($this->bankQuery()->get());
    }

    public function bankBranches(Request $request): JsonResponse
    {
        $data = $request->validate([
            'bank_area_id' => ['nullable', 'exists:thanh_pho_banks,id'],
            'tong_ngan_hang_id' => ['nullable', 'exists:tong_ngan_hang,id'],
        ]);

        return $this->success($this->bankBranchesByFilter($data));
    }

    private function districtsByProvince(string $provinceId)
    {
        $formattedProvinceId = str_pad($provinceId, 2, '0', STR_PAD_LEFT);

        return Districts::query()
            ->where('province_id', $formattedProvinceId)
            ->orderBy('name')
            ->get(['id', 'name', 'prefix', 'province_id']);
    }

    private function wardsByDistrict(string $districtId)
    {
        $formattedDistrictId = str_pad($districtId, 3, '0', STR_PAD_LEFT);

        return Wards::query()
            ->where('district_id', $formattedDistrictId)
            ->orderBy('name')
            ->get(['id', 'name', 'prefix', 'district_id']);
    }

    private function bankBranchesByFilter(array $data)
    {
        if (empty($data['bank_area_id']) && empty($data['tong_ngan_hang_id'])) {
            return [];
        }

        return NganHangBank::query()
            ->when($data['bank_area_id'] ?? null, function ($query, $bankAreaId) {
                $query->where('thanh_pho_id', $bankAreaId);
            })
            ->when($data['tong_ngan_hang_id'] ?? null, function ($query, $bankId) {
                $query->where('tong_ngan_hang_id', $bankId);
            })
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'thanh_pho_id as bank_area_id',
                'tong_ngan_hang_id',
            ]);
    }

    private function provinceQuery()
    {
        return Provinces::query()->orderBy('name')->getQuery()->select(['id', 'name', 'type']);
    }

    private function bankAreaQuery()
    {
        return ThanhPhoBanks::query()->orderBy('name')->getQuery()->select(['id', 'name']);
    }

    private function bankQuery()
    {
        return TongNganHang::query()->orderBy('name')->getQuery()->select(['id', 'name', 'ten_viet_tat']);
    }

    private function success($data = null, string $message = 'Thanh cong'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => (object) [],
            'links' => (object) [],
        ]);
    }
}
