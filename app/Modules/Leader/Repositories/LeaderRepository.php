<?php

namespace App\Modules\Leader\Repositories;

use App\Modules\Leader\Models\Leader;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LeaderRepository extends BaseRepository
{
    protected string $model = Leader::class;

    public function paginateForAdmin(?string $search): LengthAwarePaginator
    {
        return $this->paginateWithSearch($this->query()->orderBy('order_no'), $search, 'name');
    }
}
