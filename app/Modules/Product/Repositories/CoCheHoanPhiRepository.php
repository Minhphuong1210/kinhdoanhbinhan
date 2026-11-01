<?php

namespace App\Modules\Product\Repositories;

use App\Models\CoCheHoanPhi;
use App\Models\Product;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CoCheHoanPhiRepository extends BaseRepository
{
    protected string $model = CoCheHoanPhi::class;

    public function paginateForAdmin(): LengthAwarePaginator
    {
        return $this->query()->with('parent')->orderByDesc('id')->paginate(20);
    }

    public function options(?int $exceptId = null): Collection
    {
        return $this->query()
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->orderBy('name')
            ->get();
    }

    public function withAvailableProductCount(): Collection
    {
        return $this->query()
            ->withCount(['products' => fn ($q) => $q->where('active', 1)->availableForPurchase()])
            ->with('parent')
            ->orderBy('parent_id')
            ->orderBy('name')
            ->get();
    }

    public function childIds(array $parentIds): array
    {
        return $this->query()->whereIn('parent_id', $parentIds)->pluck('id')->all();
    }

    /** Gỡ liên kết của sản phẩm và cơ chế con trước khi xoá */
    public function detach(CoCheHoanPhi $coChe): void
    {
        Product::where('co_che_hoan_phi_id', $coChe->id)->update(['co_che_hoan_phi_id' => null]);
        $this->query()->where('parent_id', $coChe->id)->update(['parent_id' => null]);
    }
}
