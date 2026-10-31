<?php

namespace App\Modules\Permission\Repositories;

use App\Modules\Permission\Models\Permission;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PermissionRepository extends BaseRepository
{
    protected string $model = Permission::class;

    public function paginateForAdmin(): LengthAwarePaginator
    {
        return $this->paginateWithSearch($this->query()->latest(), null, 'name', 30);
    }
}
