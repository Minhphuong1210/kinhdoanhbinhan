<?php

namespace App\Modules\Product\Repositories;

use App\Models\Product;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository extends BaseRepository
{
    protected string $model = Product::class;

    /** Sản phẩm đang bán và còn hàng */
    public function available(): Builder
    {
        return $this->query()->where('active', 1)->availableForPurchase();
    }

    public function activeOrderedByName(): Collection
    {
        return $this->query()->where('active', 1)->orderBy('name')->get();
    }

    public function paginateForAdmin(?string $search, mixed $categoryId): LengthAwarePaginator
    {
        $query = $this->query()
            ->with('category', 'coCheHoanPhi')
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->orderBy('stt', 'asc')
            ->latest();

        return $this->paginateWithSearch($query, $search, 'name');
    }

    public function options(?int $exceptId = null): Collection
    {
        return $this->query()
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function slugExists(string $slug): bool
    {
        return $this->query()->where('slug', $slug)->exists();
    }

    /**
     * Sản phẩm được chọn khi mua tự chọn.
     * mode=custom: sản phẩm con. mode=custom_co_che: sản phẩm cùng cơ chế hoàn phí.
     */
    public function selectable(Product $product, ?string $mode, array $relations): Collection
    {
        if ($mode === 'custom_co_che' && $product->co_che_hoan_phi_id) {
            return $this->available()
                ->where('co_che_hoan_phi_id', $product->co_che_hoan_phi_id)
                ->with($relations)
                ->orderBy('stt', 'asc')
                ->orderBy('id', 'asc')
                ->get();
        }

        return $product->getActiveChildren()->load($relations);
    }

    public function paginateForShop(array $coCheIds, array $categoryIds): LengthAwarePaginator
    {
        return $this->available()
            ->with('category', 'coCheHoanPhi')
            ->withCount('children')
            ->when($coCheIds, fn ($q) => $q->whereIn('co_che_hoan_phi_id', $coCheIds))
            ->when($categoryIds, fn ($q) => $q->whereIn('category_id', $categoryIds))
            ->orderBy('stt', 'asc')
            ->paginate(16)
            ->withQueryString();
    }

    public function countInCategories(array $categoryIds, array $coCheIds): int
    {
        return $this->available()
            ->whereIn('category_id', $categoryIds)
            ->when($coCheIds, fn ($q) => $q->whereIn('co_che_hoan_phi_id', $coCheIds))
            ->count();
    }

    public function paginateForWebsite(?array $categoryIds, string $keyword, string $sort): LengthAwarePaginator
    {
        return $this->available()
            ->with('category')
            ->when($categoryIds, fn ($q) => $q->whereIn('category_id', $categoryIds))
            ->when($keyword !== '', function ($q) use ($keyword) {
                $q->where(function ($inner) use ($keyword) {
                    $inner->where('name', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhere('slug', 'like', "%{$keyword}%");
                });
            })
            ->when($sort === 'gia-tang', fn ($q) => $q->orderBy('price'))
            ->when($sort === 'gia-giam', fn ($q) => $q->orderByDesc('price'))
            ->when($sort === 'xem-nhieu', fn ($q) => $q->orderByDesc('view'))
            ->latest('id')
            ->paginate(24)
            ->withQueryString();
    }

    public function findForWebsite(string $slug): Product
    {
        return $this->available()
            ->where('slug', $slug)
            ->with(['category', 'tags'])
            ->withCount(['children as active_children_count' => fn ($q) => $q->where('active', 1)->availableForPurchase()])
            ->firstOrFail();
    }

    public function relatedInCategory(Product $product): Collection
    {
        return $this->available()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest('id')
            ->take(8)
            ->get();
    }
}
