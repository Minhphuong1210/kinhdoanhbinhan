<?php

namespace App\Modules\Location\Services;

use App\Modules\Location\Repositories\LocationRepository;
use Illuminate\Support\Collection;

/**
 * Địa giới hành chính và danh mục ngân hàng, dùng cho form đăng ký, hồ sơ và API.
 */
class LocationService
{
    public function __construct(private readonly LocationRepository $locations)
    {
    }

    public function provinces(array $columns = ['*']): Collection
    {
        return $this->locations->provinces($columns);
    }

    /** Mã tỉnh dạng 2 số: 1 → 01 */
    public function districtsByCode(string $provinceId, array $columns): Collection
    {
        return $this->locations->districts(str_pad($provinceId, 2, '0', STR_PAD_LEFT), $columns);
    }

    /** Mã huyện dạng 3 số: 1 → 001 */
    public function wardsByCode(string $districtId, array $columns): Collection
    {
        return $this->locations->wards(str_pad($districtId, 3, '0', STR_PAD_LEFT), $columns);
    }

    /** Dùng khi đã có mã đúng dạng trong hồ sơ user; chưa chọn thì trả rỗng */
    public function districtsOf(mixed $provinceId, array $columns = ['*']): Collection
    {
        return $provinceId ? $this->locations->districts((string) $provinceId, $columns) : collect();
    }

    public function wardsOf(mixed $districtId, array $columns = ['*']): Collection
    {
        return $districtId ? $this->locations->wards((string) $districtId, $columns) : collect();
    }

    public function pancakeDistricts(string $provinceId): Collection
    {
        return $this->locations->pancakeDistricts($provinceId);
    }

    public function pancakeCommunes(string $districtId): Collection
    {
        return $this->locations->pancakeCommunes($districtId);
    }

    public function bankAreas(): Collection
    {
        return $this->locations->bankAreas();
    }

    public function banks(array $columns = ['*']): Collection
    {
        return $this->locations->banks($columns);
    }

    public function branchesOfCity(int|string $cityId): Collection
    {
        return $this->locations->branchesOfCity($cityId);
    }

    public function branchesByFilter(array $data): Collection
    {
        if (empty($data['bank_area_id']) && empty($data['tong_ngan_hang_id'])) {
            return collect();
        }

        return $this->locations->branchesByFilter($data['bank_area_id'] ?? null, $data['tong_ngan_hang_id'] ?? null);
    }
}
