<?php
namespace App\Modules\Product\Services;

use App\Modules\Category\Models\Category;
use App\Modules\CategoryPost\Models\CategoryPost;
use App\Models\CoCheHoanPhi;
use App\Modules\GioiThieu\Models\GioiThieu;
use App\Modules\Post\Models\Post;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductApiService
{
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
            $coCheIds = $this->coCheIdsWithChildren((int) $filters['category_id']);
            $query->whereIn('co_che_hoan_phi_id', $coCheIds ?: [0]);
        }

        if (! empty($filters['category'])) {
            $coChe    = CoCheHoanPhi::where('id', $filters['category'])->first();
            $coCheIds = $coChe ? $this->coCheIdsWithChildren($coChe->id) : [];
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
        $coCheIds        = $this->coCheIdsWithChildren($category->id);
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

    public function newsCategories()
    {
        return CategoryPost::where('active', 1)
            ->withCount(['posts' => fn($query) => $this->publishedPostQuery($query)])
            ->orderBy('name')
            ->get()
            ->map(fn(CategoryPost $category) => [
                'id'          => $category->id,
                'name'        => $category->name,
                'slug'        => $category->slug,
                'posts_count' => $category->posts_count,
            ]);
    }

    public function news(array $filters): LengthAwarePaginator
    {
        $perPage = min((int) ($filters['per_page'] ?? 12), 50);
        $query   = Post::forWeb()->with('category')->latest();
        $this->publishedPostQuery($query);

        if (! empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_post_id', $filters['category_id']);
        }

        if (! empty($filters['category'])) {
            $category = CategoryPost::where('slug', $filters['category'])->where('active', 1)->first();
            $query->where('category_post_id', $category?->id ?? 0);
        }

        if (! empty($filters['featured'])) {
            $query->where('featured', 1);
        }

        return $query->paginate($perPage)->through(fn(Post $post) => $this->postPayload($post));
    }

    public function newsDetail(string $slug): array
    {
        $query = Post::forWeb()
            ->where('slug', $slug)
            ->with('category');
        $this->publishedPostQuery($query);

        $post = $query->firstOrFail();
        $post->increment('view');

        $relatedQuery = Post::forWeb()
            ->where('category_post_id', $post->category_post_id)
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(4);
        $this->publishedPostQuery($relatedQuery);

        return [
            'data'    => $this->postPayload($post, true),
            'related' => $relatedQuery->get()->map(fn(Post $item) => $this->postPayload($item)),
        ];
    }

    public function gioiThieuChungToi()
    {
        $gioiThieu = GioiThieu::forWeb()
            ->where('vi_tri', 've_chung_toi')
            ->firstOrFail();

        return [
            'data' => [
                ...$this->gioiThieuChungToiPayload($gioiThieu, true),

                'hero' => [
                    'badge' => 'Thương hiệu uy tín',

                    'title' => 'Sản phẩm chất lượng, giá trị bền vững và dịch vụ tin cậy.',

                    'description' => 'Chúng tôi mang đến những sản phẩm được lựa chọn kỹ lưỡng, phù hợp cho người tiêu dùng và đối tác kinh doanh, cùng hệ thống hỗ trợ rõ ràng và minh bạch.',

                    'buttons' => [
                        [
                            'title' => 'Xem sản phẩm',
                            'url' => '#products',
                            'type' => 'primary',
                        ],
                        [
                            'title' => 'Đăng ký tham gia',
                            'url' => route('admin.register'),
                            'type' => 'outline',
                        ],
                    ],

                    'statistics' => [
                        [
                            'value' => '03+',
                            'label' => 'Năm kinh doanh',
                        ],
                        [
                            'value' => '20K+',
                            'label' => 'Khách hàng tin dùng',
                        ],
                        [
                            'value' => '50+',
                            'label' => 'Mẫu sản phẩm',
                        ],
                    ],

                    'image' => [
                        'url' => 'https://cdn.kinhdoanhbinhan.com/shared/uploads/1788493393410_8942408245054837428_8942408245054837428_032ac36dd57e4ab14c076b738574a5ad.jpg',
                        'alt' => 'Đồng hành cùng doanh nghiệp phát triển bền vững',
                    ],
                ],
            ],
        ];
    }

    private function gioiThieuChungToiPayload($gioiThieu, $detail = false)
    {
        return [
            'name' => $gioiThieu->name,
            'image' => $gioiThieu->image
                ? cdn($gioiThieu->image)
                : null,
            'content' => $gioiThieu->content,
            'vi_tri' => $gioiThieu->vi_tri,
            'year' => $gioiThieu->year,
            'vi_tri_web' => $gioiThieu->vi_tri_web,
            'nam_kinh_doanh' => $gioiThieu->nam_kinh_doanh,
            'khach_hang' => $gioiThieu->khach_hang,
            'san_pham' => $gioiThieu->san_pham,
        ];
    }

    private function publishedPostQuery($query): void
    {
        $query->whereIn('status', ['published', 1, '1']);
    }

    private function categoryPayload(Category $category): array
    {
        return [
            'id'             => $category->id,
            'name'           => $category->name,
            'slug'           => $category->slug,
            'image'          => $this->assetUrl($category->image),
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
            'image'           => $this->assetUrl($product->image),
            'image_extra'     => collect($product->image_extra ?? [])->map(fn($path) => $this->assetUrl($path))->values(),
            'video'           => $this->assetUrl($product->video),
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

    private function postPayload(Post $post, bool $withContent = false): array
    {
        $data = [
            'id'          => $post->id,
            'name'        => $post->name,
            'slug'        => $post->slug,
            'image'       => $this->assetUrl($post->image),
            'description' => $post->description,
            'view'        => $post->view,
            'featured'    => (int) $post->featured,
            'created_at'  => optional($post->created_at)->toDateTimeString(),
            'category'    => $post->category ? [
                'id'   => $post->category->id,
                'name' => $post->category->name,
                'slug' => $post->category->slug,
            ] : null,
        ];

        if ($withContent) {
            $data['content']          = $post->content;
            $data['meta_title']       = $post->meta_title;
            $data['meta_keywords']    = $post->meta_keywords;
            $data['meta_description'] = $post->meta_description;
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

    private function coCheIdsWithChildren(int $coCheId): array
    {
        $ids        = [$coCheId];
        $currentIds = [$coCheId];

        while (! empty($currentIds)) {
            $childIds = CoCheHoanPhi::whereIn('parent_id', $currentIds)->pluck('id')->all();
            $childIds = array_diff($childIds, $ids);

            if (empty($childIds)) {
                break;
            }

            $ids        = array_merge($ids, $childIds);
            $currentIds = $childIds;
        }

        return $ids;
    }

    /** @var array<int, bool> */
    private array $tuChonCache = [];

    /**
     * Cơ chế mua tự chọn: trang_hoat_dong = tu_chon, trừ Bình An 0% (giống trang shop).
     */
    private function isTuChonCoChe(CoCheHoanPhi $coChe): bool
    {
        return $this->tuChonCache[$coChe->id] ??= $coChe->trang_hoat_dong === 'tu_chon'
            && ! $this->isBinhAnZeroPercentCoChe($coChe);
    }

    private function isBinhAnZeroPercentCoChe(CoCheHoanPhi $coChe): bool
    {
        $name = $this->normalizeSearchText(trim(implode(' ', array_filter([
            $coChe->name,
            $coChe->parent?->name,
        ]))));

        return str_contains($name, 'binh an') && (float) ($coChe->tang_1 ?? 0) == 0.0;
    }

    private function normalizeSearchText(string $text): string
    {
        $normalized = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

        return strtolower($normalized !== false ? $normalized : $text);
    }

    private function assetUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return function_exists('cdn') ? cdn($path) : asset($path);
    }
}
