<?php

namespace App\Modules\Shared\Services;

use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * Thêm / sửa / xoá dùng chung. Module cần xử lý riêng thì override prepare().
 */
abstract class BaseService
{
    public function __construct(protected BaseRepository $repository)
    {
    }

    public function create(array $data): Model
    {
        return $this->repository->create($this->prepare($data));
    }

    public function update(Model $model, array $data): Model
    {
        return $this->repository->update($model, $this->prepare($data));
    }

    public function delete(Model $model): void
    {
        $this->repository->delete($model);
    }

    protected function prepare(array $data): array
    {
        return $data;
    }
}
