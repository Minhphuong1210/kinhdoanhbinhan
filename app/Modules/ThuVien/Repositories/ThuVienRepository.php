<?php

namespace App\Modules\ThuVien\Repositories;

use App\Modules\ThuVien\Models\ThuVien;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ThuVienRepository extends BaseRepository
{
    protected string $model = ThuVien::class;

    public function paginateForAdmin(?string $search): LengthAwarePaginator
    {
        return $this->paginateWithSearch($this->query()->latest(), $search, 'name');
    }
}
