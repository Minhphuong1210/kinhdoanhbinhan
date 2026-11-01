<?php

namespace App\Modules\Location\Repositories;

use App\Models\Districts;
use App\Models\NganHangBank;
use App\Models\PancakeCommunes;
use App\Models\PancakeDistricts;
use App\Models\Provinces;
use App\Models\ThanhPhoBanks;
use App\Models\TongNganHang;
use App\Models\Wards;
use Illuminate\Support\Collection;

class LocationRepository
{
    public function provinces(array $columns): Collection
    {
        return Provinces::orderBy('name')->get($columns);
    }

    public function districts(string $provinceId, array $columns): Collection
    {
        return Districts::where('province_id', $provinceId)->orderBy('name')->get($columns);
    }

    public function wards(string $districtId, array $columns): Collection
    {
        return Wards::where('district_id', $districtId)->orderBy('name')->get($columns);
    }

    public function pancakeDistricts(string $provinceId): Collection
    {
        return PancakeDistricts::where('province_id', $provinceId)->orderBy('name')->get(['id_pancake as id', 'name']);
    }

    public function pancakeCommunes(string $districtId): Collection
    {
        return PancakeCommunes::where('district_id', $districtId)->orderBy('name')->get(['id_pancake as id', 'name']);
    }

    public function bankAreas(): Collection
    {
        return ThanhPhoBanks::orderBy('name')->get(['id', 'name']);
    }

    public function banks(array $columns): Collection
    {
        return TongNganHang::orderBy('name')->get($columns);
    }

    public function branchesOfCity(int|string $cityId): Collection
    {
        return NganHangBank::where('thanh_pho_id', $cityId)->orderBy('name')->get(['id', 'name']);
    }

    public function branchesByFilter(mixed $areaId, mixed $bankId): Collection
    {
        return NganHangBank::query()
            ->when($areaId, fn ($q) => $q->where('thanh_pho_id', $areaId))
            ->when($bankId, fn ($q) => $q->where('tong_ngan_hang_id', $bankId))
            ->orderBy('name')
            ->get(['id', 'name', 'thanh_pho_id as bank_area_id', 'tong_ngan_hang_id']);
    }
}
