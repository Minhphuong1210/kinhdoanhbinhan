<?php

namespace App\Modules\Product\Services;

use App\Modules\Category\Models\Category;
use App\Modules\Product\Repositories\CoCheHoanPhiRepository;
use App\Modules\Product\Repositories\ProductRepository;

/**
 * Danh sách sản phẩm cho trang shop của thành viên và website công khai.
 */
class ProductCatalogService
{
    public const SORTS = [
        'moi-nhat'  => 'Mới nhất',
        'gia-tang'  => 'Giá thấp → cao',
        'gia-giam'  => 'Giá cao → thấp',
        'xem-nhieu' => 'Xem nhiều nhất',
    ];

    public function __construct(
        private readonly ProductRepository $products,
        private readonly CoCheHoanPhiRepository $coChes,
        private readonly CoCheHoanPhiService $coCheService
    ) {
    }

    public function shopData(?string $coCheId, ?string $catId): array
    {
        $activeCoChe = $coCheId ? $this->coChes->find($coCheId) : null;
        $activeMode = ($activeCoChe && $activeCoChe->trang_hoat_dong === 'tu_chon' && !$this->coCheService->isBinhAnZeroPercent($activeCoChe))
            ? 'custom_co_che'
            : null;
        $coCheIds = $coCheId ? $this->coCheService->idsWithChildren((int) $coCheId) : [];
        $activeCatId = $catId ?? ($coCheId ? null : 1);

        return [
            'coCheHoanPhis'  => $this->coChes->withAvailableProductCount(),
            'activeCoCheId'  => $coCheId,
            'activeCoChe'    => $activeCoChe,
            'activeMode'     => $activeMode,
            'activeCatId'    => $activeCatId,
            'activeCategory' => $activeCatId ? Category::find($activeCatId) : null,
            'categories'     => $this->shopCategories($coCheId, $coCheIds),
            'products'       => $this->products->paginateForShop(
                $coCheId ? $coCheIds : [],
                $activeCatId ? $this->categoryWithChildren((int) $activeCatId) : []
            ),
        ];
    }

    public function websiteIndexData(string $keyword, ?string $sort, ?string $categorySlug): array
    {
        $sort = array_key_exists((string) $sort, self::SORTS) ? $sort : 'moi-nhat';

        $activeCategory = $categorySlug
            ? Category::where('slug', $categorySlug)->where('active', 1)->first()
            : null;

        $categoryIds = $activeCategory ? $this->categoryWithChildren($activeCategory->id) : null;

        return [
            'products'       => $this->products->paginateForWebsite($categoryIds, $keyword, $sort),
            'categories'     => Category::where('active', 1)
                ->whereNull('parent_id')
                ->withCount(['products' => fn ($q) => $q->where('active', 1)->availableForPurchase()])
                ->orderBy('name')
                ->get(),
            'activeCategory' => $activeCategory,
            'keyword'        => $keyword,
            'sort'           => $sort,
            'sorts'          => self::SORTS,
        ];
    }

    public function websiteDetailData(string $slug): array
    {
        $product = $this->products->findForWebsite($slug);
        $product->increment('view');

        return [
            'product' => $product,
            'related' => $this->products->relatedInCategory($product),
        ];
    }

    private function shopCategories(?string $coCheId, array $coCheIds)
    {
        $categories = Category::whereNull('parent_id')
            ->where('active', 1)
            ->when($coCheId, function ($q) use ($coCheIds) {
                $match = fn ($products) => $products->where('active', 1)
                    ->availableForPurchase()
                    ->whereIn('co_che_hoan_phi_id', $coCheIds);

                $q->where(fn ($sub) => $sub->whereHas('products', $match)->orWhereHas('children.products', $match));
            })
            ->orderBy('name')
            ->get();

        $categories->each(function ($category) use ($coCheIds) {
            $category->products_count = $this->products->countInCategories($this->categoryWithChildren($category->id), $coCheIds);
        });

        return $categories;
    }

    /** Danh mục kèm danh mục con trực tiếp */
    private function categoryWithChildren(int $categoryId): array
    {
        return Category::where('parent_id', $categoryId)->pluck('id')->prepend($categoryId)->all();
    }
}
