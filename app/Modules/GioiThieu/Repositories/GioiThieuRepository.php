<?php

namespace App\Modules\GioiThieu\Repositories;

use App\Modules\GioiThieu\Models\GioiThieu;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GioiThieuRepository extends BaseRepository
{
    protected string $model = GioiThieu::class;

    public function paginateForAdmin(): LengthAwarePaginator
    {
        return $this->paginateWithSearch($this->query()->latest(), null, 'name');
    }
}
