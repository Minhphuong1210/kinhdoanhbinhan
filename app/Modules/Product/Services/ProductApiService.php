<?php
namespace App\Modules\Product\Services;

use App\Modules\Category\Models\Category;
use App\Models\CoCheHoanPhi;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductApiService
{
    public function __construct(private readonly CoCheHoanPhiService $coCheService)
    {
    }

    /** Số sản phẩm mỗi trang ở API san-pham-tu-chon */
    public const TU_CHON_PER_PAGE = 20;

    public function categories()
    {
        return CoCheHoanPhi::query()
            ->with(['children' => fn($query) => $query->withCount([
                'products' => fn($query) => $query->where('active', 1)->availableForPurchase(),
            ])->orderBy('name')])
            ->withCount(['products' => fn($query) => $query->where('active', 1)->availableForPurchase()])
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get()
            ->map(fn(CoCheHoanPhi $category) => $this->coCheHoanPhiPayload($category));
    }

    public function products(array $filters): LengthAwarePaginator
    {
        $perPage = min((int) ($filters['per_page'] ?? 12), 50);
        $query   = Product::where('active', 1)
            ->availableForPurchase()
            ->with(['category', 'coCheHoanPhi'])
            ->withCount(['children as active_children_count' => fn($query) => $query->where('active', 1)->availableForPurchase()])
            ->orderBy('stt')
            ->latest();

        $search = trim((string) ($filters['search'] ?? $filters['q'] ?? ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('origin', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%");
                    })
                    ->orWhereHas('coCheHoanPhi', function ($coCheQuery) use ($search) {
                        $coCheQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['category_id'])) {
            $coCheIds = $this->coCheService->idsWithChildren((int) $filters['category_id']);
            $query->whereIn('co_che_hoan_phi_id', $coCheIds ?: [0]);
        }

        if (! empty($filters['category'])) {
            $coChe    = CoCheHoanPhi::where('id', $filters['category'])->first();
            $coCheIds = $coChe ? $this->coCheService->idsWithChildren($coChe->id) : [];
            $query->whereIn('co_che_hoan_phi_id', $coCheIds ?: [0]);
        }

        if (! empty($filters['featured'])) {
            $query->where('san_pham_noi_bat', 1);
        }

        return $query->paginate($perPage)->through(fn(Product $product) => $this->productPayload($product));
    }

    public function categoryProducts(int $categoryId, array $filters): array
    {
        $category = CoCheHoanPhi::query()
            ->with(['children' => fn($query) => $query->withCount([
                'products' => fn($query) => $query->where('active', 1)->availableForPurchase(),
            ])->orderBy('name')])
            ->withCount(['products' => fn($query) => $query->where('active', 1)->availableForPurchase()])
            ->findOrFail($categoryId);

        $perPage         = min((int) ($filters['per_page'] ?? 12), 50);
        $coCheIds        = $this->coCheService->idsWithChildren($category->id);
        $categoryBuyMode = $this->isTuChonCoChe($category)
            ? 'custom_co_che'
            : null;

        $products = Product::where('active', 1)
            ->availableForPurchase()
            ->whereIn('co_che_hoan_phi_id', $coCheIds ?: [0])
            ->with(['category', 'coCheHoanPhi'])
            ->withCount(['children as active_children_count' => fn($query) => $query->where('active', 1)->availableForPurchase()])
            ->when(trim((string) ($filters['search'] ?? $filters['q'] ?? '')) !== '', function ($query) use ($filters) {
                $search = trim((string) ($filters['search'] ?? $filters['q'] ?? ''));
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%")
                        ->orWhere('origin', 'like', "%{$search}%");
                });
            })
            ->orderBy('stt')
            ->latest()
            ->paginate($perPage)
            ->through(fn(Product $product) => $this->productPayload($product, false, false, $categoryBuyMode));

        return [
            'category' => $this->coCheHoanPhiPayload($category),
            'mode'     => $categoryBuyMode,
            'buy_mode' => $categoryBuyMode,
            'products' => $products,
        ];
    }

    /**
     * Bấm vào sản phẩm thuộc cơ chế tu_chon: trả hết sản phẩm cùng co_che_hoan_phi_id để khách chọn.
     * Trả null nếu sản phẩm không thuộc cơ chế mua tự chọn.
     */
    public function sanPhamTuChon(int $productId, array $filters): ?array
    {
        $product = Product::where('active', 1)
            ->availableForPurchase()
            ->with(['category', 'coCheHoanPhi.parent'])
            ->findOrFail($productId);

        $coChe = $product->coCheHoanPhi;

        if (! $coChe || ! $this->isTuChonCoChe($coChe)) {
            return null;
        }

        $search = trim((string) ($filters['search'] ?? $filters['q'] ?? ''));

        $products = Product::query()
            ->selectableInCoChe($coChe->id)
            ->with(['category', 'coCheHoanPhi'])
            ->withCount(['children as active_children_count' => fn($query) => $query->where('active', 1)->availableForPurchase()])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('stt')
            ->orderBy('id')
            ->paginate(self::TU_CHON_PER_PAGE)
            ->withQueryString()
            ->through(fn(Product $item) => $this->productPayload($item, false, true, 'custom_co_che'));

        return [
            'product'         => $this->productPayload($product, false, true, 'custom_co_che'),
            'co_che_hoan_phi' => $this->coCheHoanPhiPayload($coChe),
            'mode'            => 'custom_co_che',
            'products'        => $products,
        ];
    }

    public function productDetail(string $slug): array
    {
        $product = Product::where('slug', $slug)
            ->where('active', 1)
            ->availableForPurchase()
            ->with(['category', 'coCheHoanPhi', 'tags'])
            ->firstOrFail();

        $product->increment('view');

        $related = Product::where('active', 1)
            ->availableForPurchase()
            ->where('co_che_hoan_phi_id', $product->co_che_hoan_phi_id)
            ->where('id', '!=', $product->id)
            ->withCount(['children as active_children_count' => fn($query) => $query->where('active', 1)->availableForPurchase()])
            ->take(4)
            ->get()
            ->map(fn(Product $item) => $this->productPayload($item));

        return [
            'data'    => $this->productPayload($product, true),
            'related' => $related,
        ];
    }

    private function categoryPayload(Category $category): array
    {
        return [
            'id'             => $category->id,
            'name'           => $category->name,
            'slug'           => $category->slug,
            'image'          => asset_url($category->image),
            'parent_id'      => $category->parent_id,
            'products_count' => $category->products_count ?? null,
            'children'       => $category->children?->map(fn(Category $child) => $this->categoryPayload($child))->values(),
        ];
    }

    private function productPayload(Product $product, bool $withContent = false, bool $withCategoryAlias = true, ?string $buyModeOverride = null): array
    {
        $hasActiveChildren = array_key_exists('active_children_count', $product->getAttributes())
            ? (int) $product->active_children_count > 0
            : $product->children()->where('active', 1)->availableForPurchase()->exists();
        $buyMode = $buyModeOverride ?: ($hasActiveChildren ? 'custom' : null);

        $data = [
            'id'              => $product->id,
            'name'            => $product->name,
            'slug'            => $product->slug,
            'price'           => $product->price,
            'quantity'        => $product->quantity,
            'in_stock'        => $product->quantity === null || (int) $product->quantity > 0,
            'old_price'       => $product->old_price,
            'price_hoan_tien' => $product->price_hoan_tien,
            'purchase_price'  => $product->purchase_price,
            'image'           => asset_url($product->image),
            'image_extra'     => collect($product->image_extra ?? [])->map(fn($path) => asset_url($path))->values(),
            'video'           => asset_url($product->video),
            'description'     => $product->description,
            'view'            => $product->view,
            'childre_product' => $hasActiveChildren,
            'is_tu_chon'      => $product->coCheHoanPhi ? $this->isTuChonCoChe($product->coCheHoanPhi) : false,
            'mode'            => $buyMode,
            'buy_mode'        => $buyMode,
            'purchase_mode'   => $buyMode,
            'legacy_category' => $product->category ? [
                'id'   => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
            ] : null,
            'co_che_hoan_phi' => $product->coCheHoanPhi ? [
                'id'              => $product->coCheHoanPhi->id,
                'name'            => $product->coCheHoanPhi->name,
                'parent_id'       => $product->coCheHoanPhi->parent_id,
                'trang_hoat_dong' => $product->coCheHoanPhi->trang_hoat_dong,
            ] : null,
        ];

        if ($withCategoryAlias) {
            $data['category'] = $product->coCheHoanPhi ? [
                'id'              => $product->coCheHoanPhi->id,
                'name'            => $product->coCheHoanPhi->name,
                'parent_id'       => $product->coCheHoanPhi->parent_id,
                'trang_hoat_dong' => $product->coCheHoanPhi->trang_hoat_dong,
            ] : null;
        }

        if ($withContent) {
            $data['content']      = $product->content;
            $data['instructions'] = $product->instructions;
            $data['licenses']     = $product->licenses;
            $data['origin']       = $product->origin;
            $data['tags']         = $product->relationLoaded('tags')
                ? $product->tags->map(fn($tag) => ['id' => $tag->id, 'name' => $tag->name, 'slug' => $tag->slug])->values()
                : [];
        }

        return $data;
    }

    private function coCheHoanPhiPayload(CoCheHoanPhi $coChe): array
    {
        return [
            'id'              => $coChe->id,
            'name'            => $coChe->name,
            'parent_id'       => $coChe->parent_id,
            'trang_hoat_dong' => $coChe->trang_hoat_dong,
            'products_count'  => $coChe->products_count ?? null,
            'children'        => $coChe->children?->map(fn(CoCheHoanPhi $child) => $this->coCheHoanPhiPayload($child))->values(),
        ];
    }

    /** @var array<int, bool> */
    private array $tuChonCache = [];

    /**
     * Cơ chế mua tự chọn: trang_hoat_dong = tu_chon, trừ Bình An 0% (giống trang shop).
     */
    private function isTuChonCoChe(CoCheHoanPhi $coChe): bool
    {
        return $this->tuChonCache[$coChe->id] ??= $coChe->trang_hoat_dong === 'tu_chon'
            && ! $this->coCheService->isBinhAnZeroPercent($coChe);
    }

}
