<?php

namespace App\Modules\ChinhSach\Repositories;

use App\Modules\ChinhSach\Models\ChinhSach;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ChinhSachRepository extends BaseRepository
{
    protected string $model = ChinhSach::class;

    public function paginateForAdmin(?string $search): LengthAwarePaginator
    {
        return $this->paginateWithSearch($this->query()->latest(), $search, 'title');
    }
}
