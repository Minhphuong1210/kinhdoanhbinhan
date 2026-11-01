<?php

namespace App\Modules\Permission\Services;

use App\Modules\Permission\Repositories\RoleRepository;
use App\Modules\Shared\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class RoleService extends BaseService
{
    public function __construct(RoleRepository $repository)
    {
        parent::__construct($repository);
    }

    public function list(): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin();
    }

    public function allOrdered()
    {
        return $this->repository->query()->orderBy('name')->get();
    }

    public function create(array $data): Model
    {
        $permissions = Arr::pull($data, 'permissions', []);
        $role = parent::create($data);
        $role->permissions()->sync($permissions);

        return $role;
    }

    public function update(Model $model, array $data): Model
    {
        $permissions = Arr::pull($data, 'permissions', []);
        parent::update($model, $data);
        $model->permissions()->sync($permissions);

        return $model;
    }
}
