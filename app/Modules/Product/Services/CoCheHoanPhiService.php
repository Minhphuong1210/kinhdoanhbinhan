<?php

namespace App\Modules\Product\Services;

use App\Models\CoCheHoanPhi;
use App\Modules\Product\Repositories\CoCheHoanPhiRepository;
use App\Modules\Shared\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CoCheHoanPhiService extends BaseService
{
    public function __construct(CoCheHoanPhiRepository $repository)
    {
        parent::__construct($repository);
    }

    public function list(): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin();
    }

    public function options(?int $exceptId = null): Collection
    {
        return $this->repository->options($exceptId);
    }

    /** ID cơ chế và toàn bộ cơ chế con các cấp */
    public function idsWithChildren(int $coCheId): array
    {
        $ids = [$coCheId];
        $currentIds = [$coCheId];

        while (!empty($currentIds)) {
            $childIds = array_values(array_diff($this->repository->childIds($currentIds), $ids));
            $ids = array_merge($ids, $childIds);
            $currentIds = $childIds;
        }

        return $ids;
    }

    public function isBinhAnZeroPercent(CoCheHoanPhi $coChe): bool
    {
        $source = normalize_search_text(trim(implode(' ', array_filter([
            $coChe->name,
            $coChe->parent?->name,
        ]))));

        return str_contains($source, 'binh an') && (float) ($coChe->tang_1 ?? 0) == 0.0;
    }

    public function delete(Model $model): void
    {
        $this->repository->detach($model);

        parent::delete($model);
    }

    protected function prepare(array $data): array
    {
        for ($i = 1; $i <= 6; $i++) {
            $data['tang_' . $i] = (float) ($data['tang_' . $i] ?? 0);
        }

        $data['parent_id'] = $data['parent_id'] ?? null;

        return $data;
    }
}
