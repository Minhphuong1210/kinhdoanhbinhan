<?php

namespace App\Modules\Permission\Services;

use App\Modules\Permission\Repositories\PermissionRepository;
use App\Modules\Shared\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PermissionService extends BaseService
{
    public function __construct(PermissionRepository $repository)
    {
        parent::__construct($repository);
    }

    public function list(): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin();
    }

    public function all()
    {
        return $this->repository->all();
    }
}
