<?php

namespace App\Modules\User\Services;

use App\Models\User;
use App\Modules\Shared\Services\BaseService;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

/**
 * Admin quản lý thành viên.
 */
class UserService extends BaseService
{
    protected ?string $uploadFolder = 'avatars';

    public function __construct(UserRepository $repository, private readonly UserTreeService $tree)
    {
        parent::__construct($repository);
    }

    public function list(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin($filters);
    }

    public function search(?string $term): Collection
    {
        return $this->repository->search($term);
    }

    public function parentOptions(?int $exceptId = null): Collection
    {
        return $this->repository->parentOptions($exceptId);
    }

    public function create(array $data): Model
    {
        $roles = Arr::pull($data, 'roles');
        $data['password'] = Hash::make($data['password']);
        $data['active'] = $data['active'] ?? true;
        $data['vi_tien'] = $data['vi_tien'] ?? 0;

        $user = parent::create($data);

        if (!empty($roles)) {
            $user->roles()->sync($roles);
        }

        return $user;
    }

    public function update(Model $model, array $data): Model
    {
        $roles = Arr::pull($data, 'roles', []);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        parent::update($model, $data);
        $model->roles()->sync($roles ?? []);

        return $model;
    }

    public function delete(Model $model): void
    {
        $this->tree->deleteAndRebuildTree($model);
    }

    protected function prepare(array $data): array
    {
        $data['active'] = $this->flag($data, 'active');
        $data['is_admin'] = $this->flag($data, 'is_admin');
        unset($data['password_confirmation']);

        return $data;
    }
}
