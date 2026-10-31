<?php

namespace App\Modules\Order\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PancakeCommunes;
use App\Models\PancakeDistricts;
use App\Models\PancakeProvinces;
use App\Models\Product;
use App\Modules\Cart\Services\CartService;
use App\Modules\Payment\Services\CheckoutService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class OrderApiController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly CheckoutService $checkoutService,
        private readonly CartService $cartService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $data = $this->validateOrderFilters($request);

        $orders = $this->filteredOrdersQuery($request, $data, 'created_at')
            ->latest()
            ->paginate((int) ($data['per_page'] ?? 20));

        return $this->paginated($orders, $orders->getCollection()->map(fn (Order $order) => $this->orderPayload($order)));
    }

    /**
     * Đơn đặt qua app, sắp xếp theo thời gian mua từ cũ nhất đến mới nhất.
     * date_from/date_to lọc theo thoi_gian_mua.
     */
    public function theoThoiGianMua(Request $request): JsonResponse
    {
        $data = $this->validateOrderFilters($request);

        // Chỉ đơn đặt qua app mới có thoi_gian_mua
        $orders = $this->filteredOrdersQuery($request, $data, 'thoi_gian_mua')
            ->whereNotNull('thoi_gian_mua')
            ->orderBy('thoi_gian_mua')
            ->orderBy('id')
            ->paginate((int) ($data['per_page'] ?? 20));

        return $this->paginated($orders, $orders->getCollection()->map(fn (Order $order) => $this->orderPayload($order)));
    }

    private function validateOrderFilters(Request $request): array
    {
        return $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:pending,confirmed,shipping,completed,cancelled'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'device' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'all' => ['nullable', 'boolean'],
        ]);
    }

    private function filteredOrdersQuery(Request $request, array $data, string $dateColumn)
    {
        $user = $request->user();
        $canViewAll = ($user->is_admin || $user->hasPermission('order.view')) && ($data['all'] ?? false);

        return Order::query()
            ->with(['user:id,name,tel,email', 'details.product.category', 'details.product.coCheHoanPhi'])
            ->when(!$canViewAll, fn ($query) => $query->where('user_id', $user->id))
            ->when($data['search'] ?? null, function ($query, string $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ma_don_hang', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('receive_user', 'like', "%{$search}%")
                        ->orWhere('receive_phone', 'like', "%{$search}%");
                });
            })
            ->when($data['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($data['date_from'] ?? null, fn ($query, string $date) => $query->whereDate($dateColumn, '>=', $date))
            ->when($data['date_to'] ?? null, fn ($query, string $date) => $query->whereDate($dateColumn, '<=', $date))
            ->when($data['device'] ?? null, fn ($query, string $device) => $query->where('device', 'like', "%{$device}%"))
            ->when($data['product_id'] ?? null, function ($query, int $productId) {
                $query->whereHas('details', fn ($q) => $q->where('product_id', $productId));
            });
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();

        if ($order->user_id !== $user->id && !$user->is_admin && !$user->hasPermission('order.view')) {
            abort(403);
        }

        $order->load([
            'user:id,name,tel,email',
            'details.product.category',
            'details.product.coCheHoanPhi',
            'province:id,name,type',
            'district:id,name,prefix',
            'ward:id,name,prefix',
        ]);

        return $this->success([
            'order' => $this->orderPayload($order, true),
        ]);
    }

    public function buyInfo(Request $request, Product $product): JsonResponse
    {
        abort_unless((int) $product->active === 1, 404);
        abort_unless($product->quantity === null || (int) $product->quantity > 0, 404);

        $data = $request->validate([
            'mode' => ['nullable', 'in:custom,custom_co_che'],
        ]);

        $mode = $data['mode'] ?? null;
        $isCustom = in_array($mode, ['custom', 'custom_co_che'], true);
        $children = collect();

        if ($isCustom) {
            $children = $mode === 'custom_co_che' && $product->co_che_hoan_phi_id
                ? Product::query()
                    ->where('active', 1)
                    ->availableForPurchase()
                    ->where('co_che_hoan_phi_id', $product->co_che_hoan_phi_id)
                    ->with(['category', 'coCheHoanPhi'])
                    ->orderBy('stt', 'asc')
                    ->orderBy('id', 'asc')
                    ->get()
                : $product->getActiveChildren()->load(['category', 'coCheHoanPhi']);
        }

        $qty = max(1, (int) $request->input('qty', 1));
        [$total, $totalHoan] = $this->orderService->tinhTongTien($product, $qty);
        $wallet = (float) ($request->user()->vi_tien_mua_hang ?? 0);

        return $this->success([
            'user' => $this->buyerPayload($request->user()),
            'product' => $this->productPayload($product->loadMissing(['category', 'coCheHoanPhi'])),
            'mode' => $mode,
            'is_custom' => $isCustom,
            'children' => $children->map(fn (Product $child) => $this->productPayload($child))->values(),
            'provinces' => PancakeProvinces::whereNotNull('new_id')
                ->orderBy('name')
                ->get(['id_pancake as id', 'name'])
                ->map(fn ($province) => ['id' => $province->id, 'name' => $province->name])
                ->values(),
            'summary' => [
                'qty' => $qty,
                'total_price' => (float) $total,
                'total_price_hoan_tien' => (float) $totalHoan,
                'wallet' => $wallet,
                'can_pay_by_wallet' => $wallet >= $total,
                'missing_amount' => max(0, $total - $wallet),
            ],
        ]);
    }

    public function previewTuChon(Request $request): JsonResponse
    {
        $this->normalizeCustomItems($request);

        $data = Validator::make($request->all(), [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'mode' => ['nullable', 'in:custom,custom_co_che'],
            'child_ids' => ['required', 'array', 'min:1'],
            'child_ids.*' => ['required', 'integer', 'exists:products,id'],
            'child_qtys' => ['required', 'array', 'min:1'],
            'child_qtys.*' => ['required', 'integer', 'min:0'],
            'receive_user' => ['nullable', 'string', 'max:255'],
            'receive_phone' => ['nullable', 'string', 'max:20'],
            'province_id' => ['nullable'],
            'district_id' => ['nullable'],
            'ward_id' => ['nullable'],
            'receive_address' => ['nullable', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:500'],
        ])->validate();

        $preview = $this->customPreviewPayload($request, $data);

        return $this->success($preview);
    }

    public function muaCombo(Request $request): JsonResponse
    {
        $data = $this->validatePurchase($request, false);
        $product = Product::where('active', 1)->availableForPurchase()->findOrFail($data['product_id']);

        $order = $this->createOrderWithLock($request, function () use ($request, $product) {
            return $this->orderService->muaGoi($request, $product, true);
        });

        $order->load([
            'user:id,name,tel,email',
            'details.product.category',
            'details.product.coCheHoanPhi',
            'province:id,name,type',
            'district:id,name,prefix',
            'ward:id,name,prefix',
        ]);

        return $this->success([
            'order' => $this->orderPayload($order, true),
            'user' => $this->buyerPayload($request->user()->fresh()),
        ], 'Dat mua thanh cong', 201);
    }

    public function muaTuChon(Request $request): JsonResponse
    {
        $this->normalizeCustomItems($request);
        $data = $this->validatePurchase($request, true);
        $product = Product::where('active', 1)->availableForPurchase()->findOrFail($data['product_id']);

        $order = $this->createOrderWithLock($request, function () use ($request, $product) {
            return $this->orderService->muaTuChon($request, $product, true);
        });

        // Đặt thành công thì bỏ các sản phẩm vừa mua khỏi giỏ hàng
        $this->cartService->removePurchased($request->user(), $product->id, (array) $request->input('child_ids', []));

        $order->load([
            'user:id,name,tel,email',
            'details.product.category',
            'details.product.coCheHoanPhi',
            'province:id,name,type',
            'district:id,name,prefix',
            'ward:id,name,prefix',
        ]);

        return $this->success([
            'order' => $this->orderPayload($order, true),
            'user' => $this->buyerPayload($request->user()->fresh()),
        ], 'Dat mua thanh cong', 201);
    }

    private function customPreviewPayload(Request $request, array $data): array
    {
        $parentProduct = Product::where('active', 1)
            ->availableForPurchase()
            ->with(['category', 'coCheHoanPhi'])
            ->findOrFail($data['product_id']);
        $products = Product::where('active', 1)
            ->availableForPurchase()
            ->whereIn('id', $data['child_ids'])
            ->with(['category', 'coCheHoanPhi'])
            ->get()
            ->keyBy('id');

        $items = collect();
        foreach ($data['child_ids'] as $index => $id) {
            $qty = (int) ($data['child_qtys'][$index] ?? 0);
            $product = $products->get((int) $id);

            if ($qty <= 0 || !$product) {
                continue;
            }

            $items->push([
                'product' => $this->productPayload($product),
                'quantity' => $qty,
                'unit_price' => (float) ($product->price ?? 0),
                'line_total' => (float) ($product->price ?? 0) * $qty,
            ]);
        }

        if ($items->isEmpty()) {
            abort(response()->json([
                'success' => false,
                'message' => 'Vui long chon it nhat 1 san pham voi so luong lon hon 0.',
                'data' => null,
                'meta' => (object) [],
                'links' => (object) [],
            ], 422));
        }

        $shippingFee = OrderService::SHIPPING_FEE_TU_CHON;
        $total = (float) $items->sum('line_total');
        $payableTotal = $total + $shippingFee;
        $totalHoan = max(0, $total - $shippingFee);
        $wallet = (float) ($request->user()->vi_tien_mua_hang ?? 0);

        return [
            'product' => $this->productPayload($parentProduct),
            'mode' => $data['mode'] ?? 'custom',
            'items' => $items->values(),
            'receiver' => $this->receiverPreviewPayload($data),
            'summary' => [
                'total_product_price' => $total,
                'shipping_price' => $shippingFee,
                'payable_total' => $payableTotal,
                'total_price_hoan_tien' => $totalHoan,
                'wallet' => $wallet,
                'can_pay_by_wallet' => $wallet >= $payableTotal,
                'missing_amount' => max(0, $payableTotal - $wallet),
            ],
        ];
    }

    private function validatePurchase(Request $request, bool $custom): array
    {
        $rules = [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'qty' => ['nullable', 'integer', 'min:1'],
            'receive_user' => ['required', 'string', 'max:255'],
            'receive_phone' => ['required', 'string', 'max:20'],
            'province_id' => ['required'],
            'district_id' => ['required'],
            'ward_id' => ['required'],
            'receive_address' => ['required', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:500'],
            'device' => ['nullable', 'string', 'max:255'],
        ];

        if ($custom) {
            $rules['mode'] = ['nullable', 'in:custom,custom_co_che'];
            $rules['child_ids'] = ['required', 'array', 'min:1'];
            $rules['child_ids.*'] = ['required', 'integer', 'exists:products,id'];
            $rules['child_qtys'] = ['required', 'array', 'min:1'];
            $rules['child_qtys.*'] = ['required', 'integer', 'min:0'];
        }

        return Validator::make($request->all(), $rules, [
            'product_id.required' => 'Vui long chon san pham.',
            'receive_user.required' => 'Vui long nhap ten nguoi nhan.',
            'receive_phone.required' => 'Vui long nhap so dien thoai.',
            'province_id.required' => 'Vui long chon tinh/thanh.',
            'district_id.required' => 'Vui long chon quan/huyen.',
            'ward_id.required' => 'Vui long chon phuong/xa.',
            'receive_address.required' => 'Vui long nhap dia chi cu the.',
            'child_ids.required' => 'Vui long chon it nhat 1 san pham.',
            'child_qtys.required' => 'Vui long nhap so luong san pham.',
        ])->validate();
    }

    private function buyerPayload($user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'tel' => $user->tel,
            'wallet' => [
                'vi_tien' => (float) ($user->vi_tien ?? 0),
                'vi_tien_mua_hang' => (float) ($user->vi_tien_mua_hang ?? 0),
            ],
            'default_receiver' => [
                'name' => $user->name,
                'phone' => $user->tel,
                'address' => $user->address ?? null,
                'province_id' => $user->province_id ?? null,
                'district_id' => $user->district_id ?? null,
                'ward_id' => $user->ward_id ?? null,
            ],
        ];
    }

    private function productPayload(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => (float) ($product->price ?? 0),
            'quantity' => $product->quantity,
            'in_stock' => $product->quantity === null || (int) $product->quantity > 0,
            'old_price' => (float) ($product->old_price ?? 0),
            'price_hoan_tien' => (float) ($product->price_hoan_tien ?? 0),
            'purchase_price' => (float) ($product->purchase_price ?? 0),
            'image' => $this->assetUrl($product->image),
            'image_extra' => collect($product->image_extra ?? [])->map(fn ($path) => $this->assetUrl($path))->values(),
            'description' => $product->description,
            'content' => $product->content,
            'instructions' => $product->instructions,
            'licenses' => $product->licenses,
            'origin' => $product->origin,
            'parent_id' => $product->parent_id,
            'co_che_hoan_phi_id' => $product->co_che_hoan_phi_id,
            'san_pham_hoan' => (int) ($product->san_pham_hoan ?? 0),
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
            ] : null,
            'co_che_hoan_phi' => $product->coCheHoanPhi ? [
                'id' => $product->coCheHoanPhi->id,
                'name' => $product->coCheHoanPhi->name,
                'parent_id' => $product->coCheHoanPhi->parent_id,
                'trang_hoat_dong' => $product->coCheHoanPhi->trang_hoat_dong,
            ] : null,
        ];
    }

    private function receiverPreviewPayload(array $data): array
    {
        $province = !empty($data['province_id'])
            ? PancakeProvinces::where('id_pancake', $data['province_id'])->first()
            : null;
        $district = !empty($data['district_id'])
            ? PancakeDistricts::where('id_pancake', $data['district_id'])->first()
            : null;
        $ward = !empty($data['ward_id'])
            ? PancakeCommunes::where('id_pancake', $data['ward_id'])->first()
            : null;

        return [
            'name' => $data['receive_user'] ?? null,
            'phone' => $data['receive_phone'] ?? null,
            'address' => $data['receive_address'] ?? null,
            'province_id' => $data['province_id'] ?? null,
            'district_id' => $data['district_id'] ?? null,
            'ward_id' => $data['ward_id'] ?? null,
            'province' => $province?->name,
            'district' => $district?->name,
            'ward' => $ward?->name,
            'note' => $data['note'] ?? null,
        ];
    }

    private function normalizeCustomItems(Request $request): void
    {
        if ($request->filled('child_ids') || !$request->filled('items')) {
            return;
        }

        $childIds = [];
        $childQtys = [];

        foreach ((array) $request->input('items', []) as $item) {
            $childIds[] = $item['product_id'] ?? $item['id'] ?? null;
            $childQtys[] = $item['quantity'] ?? $item['qty'] ?? 0;
        }

        $request->merge([
            'child_ids' => $childIds,
            'child_qtys' => $childQtys,
        ]);
    }

    private function createOrderWithLock(Request $request, callable $callback): Order
    {
        $user = $request->user();
        Auth::setUser($user);

        $lock = Cache::lock('api_order_lock_' . $user->id, 10);

        if (!$lock->get()) {
            abort(response()->json([
                'success' => false,
                'message' => 'Don hang dang duoc xu ly, vui long doi.',
                'data' => null,
                'meta' => (object) [],
                'links' => (object) [],
            ], 429));
        }

        try {
            return $callback();
        } catch (\Exception $e) {
            $status = (int) $e->getCode() === 402 ? 402 : 422;
            $payment = $status === 402 ? $this->createTopupPaymentPayload($request) : null;

            abort(response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => $payment,
                'meta' => (object) [],
                'links' => (object) [],
            ], $status));
        } finally {
            $lock->release();
        }
    }

    private function createTopupPaymentPayload(Request $request): ?array
    {
        $user = $request->user();
        $payment = $this->checkoutService->createMissingAmountTopup($request->all(), $user);

        if (!$payment) {
            return null;
        }

        return [
            'requires_topup' => true,
            'payment_id' => $payment->id,
            'order_code' => $payment->ma_don,
            'amount' => $payment->so_tien,
            'checkout_url' => $payment->link,
            'payment_url' => $payment->link,
            'wallet' => (float) ($user->vi_tien_mua_hang ?? 0),
            'missing_amount' => $payment->so_tien,
        ];
    }

    private function orderPayload(Order $order, bool $includeAddress = false): array
    {
        $payload = [
            'id' => $order->id,
            'ma_don_hang' => $order->ma_don_hang,
            'name' => $order->name,
            'status' => $order->status,
            'total_price' => (float) ($order->total_price ?? 0),
            'total_price_hoan_tien' => (float) ($order->total_price_hoan_tien ?? 0),
            'price_vat' => (float) ($order->price_vat ?? 0),
            'shipping_price' => (float) ($order->shipping_price ?? 0),
            'total_quantity' => (int) $order->details->sum('quantity'),
            'first_order' => (int) ($order->first_order ?? 0),
            'device' => $order->device,
            'thoi_gian_mua' => $this->dateTimeString($order->thoi_gian_mua),
            'created_at' => $this->dateTimeString($order->created_at),
            'updated_at' => $this->dateTimeString($order->updated_at),
            'user' => $order->user ? [
                'id' => $order->user->id,
                'name' => $order->user->name,
                'tel' => $order->user->tel,
                'email' => $order->user->email,
            ] : null,
            'items' => $order->details->map(fn ($detail) => [
                'id' => $detail->id,
                'product_id' => $detail->product_id,
                'name' => $detail->name_product,
                'image' => $this->assetUrl($detail->image_product ?: $detail->product?->image),
                'price' => (float) ($detail->price_product ?? 0),
                'quantity' => (int) ($detail->quantity ?? 0),
                'line_total' => (float) ($detail->price_product ?? 0) * (int) ($detail->quantity ?? 0),
                'type' => $detail->the_loai ?? null,
                'product' => $detail->product ? $this->productPayload($detail->product) : null,
            ])->values(),
            'summary' => [
                'total_product_price' => (float) ($order->total_price ?? 0),
                'shipping_price' => (float) ($order->shipping_price ?? 0),
                'price_vat' => (float) ($order->price_vat ?? 0),
                'payable_total' => (float) ($order->total_price ?? 0),
                'total_price_hoan_tien' => (float) ($order->total_price_hoan_tien ?? 0),
            ],
        ];

        if ($includeAddress) {
            $payload['receiver'] = [
                'name' => $order->receive_user,
                'phone' => $order->receive_phone,
                'address' => $order->receive_address,
                'province_id' => $order->province_id,
                'district_id' => $order->district_id,
                'ward_id' => $order->ward_id,
                'province' => $order->province?->name,
                'district' => $order->district?->name,
                'ward' => $order->ward?->name,
                'note' => $order->note,
            ];
        }

        return $payload;
    }

    private function paginated($paginator, $items): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Thanh cong',
            'data' => $items,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ]);
    }

    private function success($data = null, string $message = 'Thanh cong', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => (object) [],
            'links' => (object) [],
        ], $status);
    }

    private function dateTimeString($date): ?string
    {
        return $date ? $date->format('Y-m-d H:i:s') : null;
    }

    private function assetUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return function_exists('cdn') ? cdn($path) : asset($path);
    }
}
