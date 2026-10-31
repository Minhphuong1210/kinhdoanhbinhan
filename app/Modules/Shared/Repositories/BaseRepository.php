<?php

namespace App\Modules\Shared\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Truy vấn dùng chung. Repository của từng module kế thừa và khai báo $model.
 */
abstract class BaseRepository
{
    /** @var class-string<Model> */
    protected string $model;

    public function query(): Builder
    {
        return $this->model::query();
    }

    public function all(): Collection
    {
        return $this->query()->get();
    }

    public function find(int|string $id): ?Model
    {
        return $this->query()->find($id);
    }

    public function create(array $data): Model
    {
        return $this->query()->create($data);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);

        return $model;
    }

    public function delete(Model $model): void
    {
        $model->delete();
    }
}
