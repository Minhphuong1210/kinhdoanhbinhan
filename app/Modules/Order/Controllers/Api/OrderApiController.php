<?php

namespace App\Modules\Order\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Modules\Cart\Services\CartService;
use App\Modules\Order\Repositories\OrderRepository;
use App\Modules\Order\Services\OrderAccessService;
use App\Modules\Order\Services\OrderPayloadService;
use App\Modules\Order\Services\ShopService;
use App\Modules\Payment\Services\CheckoutService;
use App\Modules\Product\Repositories\ProductRepository;
use App\Modules\Shared\Traits\ApiResponse;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class OrderApiController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly OrderService $orderService,
        private readonly CheckoutService $checkoutService,
        private readonly CartService $cartService,
        private readonly OrderRepository $orders,
        private readonly OrderAccessService $access,
        private readonly OrderPayloadService $payloads,
        private readonly ProductRepository $products,
        private readonly ShopService $shopService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $data = $this->validateOrderFilters($request);

        $orders = $this->ordersFor($request, $data, 'created_at')
            ->latest()
            ->paginate((int) ($data['per_page'] ?? 20));

        return $this->paginated($orders, $orders->getCollection()->map(fn (Order $order) => $this->payloads->orderPayload($order)));
    }

    /**
     * Đơn đặt qua app, sắp xếp theo thời gian mua từ cũ nhất đến mới nhất.
     * date_from/date_to lọc theo thoi_gian_mua.
     */
    public function theoThoiGianMua(Request $request): JsonResponse
    {
        $data = $this->validateOrderFilters($request);

        // Chỉ đơn đặt qua app mới có thoi_gian_mua
        $orders = $this->ordersFor($request, $data, 'thoi_gian_mua')
            ->whereNotNull('thoi_gian_mua')
            ->orderBy('thoi_gian_mua')
            ->orderBy('id')
            ->paginate((int) ($data['per_page'] ?? 20));

        return $this->paginated($orders, $orders->getCollection()->map(fn (Order $order) => $this->payloads->orderPayload($order)));
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

    private function ordersFor(Request $request, array $data, string $dateColumn)
    {
        $user = $request->user();
        $canViewAll = $this->access->canViewAll($user) && ($data['all'] ?? false);

        return $this->orders->forApi($canViewAll ? null : $user->id, $data, $dateColumn);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        abort_unless($this->access->canView($request->user(), $order), 403);

        $order->load([
            'user:id,name,tel,email',
            'details.product.category',
            'details.product.coCheHoanPhi',
            'province:id,name,type',
            'district:id,name,prefix',
            'ward:id,name,prefix',
        ]);

        return $this->success([
            'order' => $this->payloads->orderPayload($order, true),
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
        $children = $isCustom
            ? $this->products->selectable($product, $mode, ['category', 'coCheHoanPhi'])
            : collect();

        $qty = max(1, (int) $request->input('qty', 1));
        [$total, $totalHoan] = $this->orderService->tinhTongTien($product, $qty);
        $wallet = (float) ($request->user()->vi_tien_mua_hang ?? 0);

        return $this->success([
            'user' => $this->payloads->buyerPayload($request->user()),
            'product' => $this->payloads->productPayload($product->loadMissing(['category', 'coCheHoanPhi'])),
            'mode' => $mode,
            'is_custom' => $isCustom,
            'children' => $children->map(fn (Product $child) => $this->payloads->productPayload($child))->values(),
            'provinces' => $this->shopService->provinces()
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

        $preview = $this->payloads->customPreviewPayload($request, $data);

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
            'order' => $this->payloads->orderPayload($order, true),
            'user' => $this->payloads->buyerPayload($request->user()->fresh()),
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
            'order' => $this->payloads->orderPayload($order, true),
            'user' => $this->payloads->buyerPayload($request->user()->fresh()),
        ], 'Dat mua thanh cong', 201);
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

}
