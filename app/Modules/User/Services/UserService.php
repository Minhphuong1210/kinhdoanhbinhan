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
        $data = $this->withoutPrivilegedFields($data);
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
        $canManage = $this->canManagePrivileges();
        $data = $this->withoutPrivilegedFields($data);
        $roles = Arr::pull($data, 'roles', []);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        parent::update($model, $data);

        if ($canManage) {
            $model->roles()->sync($roles ?? []);
        }

        return $model;
    }

    public function delete(Model $model): void
    {
        $this->tree->deleteAndRebuildTree($model);
    }

    /** Chỉ admin mới được đổi quyền admin, role và số dư */
    private function canManagePrivileges(): bool
    {
        return (bool) auth()->user()?->is_admin;
    }

    private function withoutPrivilegedFields(array $data): array
    {
        return $this->canManagePrivileges() ? $data : Arr::except($data, ['is_admin', 'vi_tien', 'roles']);
    }

    protected function prepare(array $data): array
    {
        $data['active'] = $this->flag($data, 'active');

        if ($this->canManagePrivileges()) {
            $data['is_admin'] = $this->flag($data, 'is_admin');
        }

        unset($data['password_confirmation']);

        return $data;
    }
}
