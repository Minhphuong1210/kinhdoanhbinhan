<?php

namespace App\Modules\Permission\Repositories;

use App\Modules\Permission\Models\Role;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RoleRepository extends BaseRepository
{
    protected string $model = Role::class;

    public function paginateForAdmin(): LengthAwarePaginator
    {
        return $this->paginateWithSearch($this->query()->withCount('permissions')->latest(), null, 'name');
    }
}
