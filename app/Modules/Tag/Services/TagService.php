<?php

namespace App\Modules\Tag\Services;

use App\Modules\Shared\Services\BaseService;
use App\Modules\Tag\Repositories\TagRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class TagService extends BaseService
{
    public function __construct(TagRepository $repository)
    {
        parent::__construct($repository);
    }

    public function list(?string $search): LengthAwarePaginator
    {
        return $this->repository->paginateWithProductCount($search);
    }

    protected function prepare(array $data): array
    {
        $data['slug'] = Str::slug($data['name']);
        // Form gửi 'active' / 'inactive', cột status là số 1 / 0
        $data['status'] = ($data['status'] ?? null) === 'active' ? 1 : 0;

        return $data;
    }
}
