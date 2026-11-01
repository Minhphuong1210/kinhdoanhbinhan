<?php

namespace App\Modules\Order\Controllers\Admin;

use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Modules\Order\Repositories\OrderRepository;
use App\Modules\Order\Services\OrderAccessService;
use App\Modules\Payment\Controllers\Admin\CheckoutController;
use App\Modules\Payment\Rules\ReceiverFullName;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $service,
        private readonly OrderRepository $orders,
        private readonly OrderAccessService $access
    ) {
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $this->access->canViewAll($user);
        $products = Product::where('active', 1)->get(['id', 'name']);

        $orders = $isAdmin
            ? $this->orders->paginateForAdmin($request->only(['search', 'status', 'product_id', 'date_from', 'date_to']))
            : $this->orders->paginateOfUser($user->id);

        return view('Order::Admin.index', compact('orders', 'isAdmin', 'products'));
    }

    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        $userId = $this->access->canViewAll($user) ? null : $user->id;

        $orders = $this->orders->forExport($userId, $request->only(['status', 'product_id', 'date_from', 'date_to']));
        $from = $request->date_from ? '_' . $request->date_from . '_' . ($request->date_to ?: now()->toDateString()) : '';

        return Excel::download(new OrdersExport($orders), 'Don-hang' . $from . '.xlsx');
    }

    public function show(Order $order)
    {
        abort_unless($this->access->canView(auth()->user(), $order), 403);

        $order->load('user', 'details.product');

        return view('Order::Admin.show', compact('order'));
    }

    public function edit(Order $order)
    {
        abort_unless($this->access->canEdit(auth()->user()), 403);

        return view('Order::Admin.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        abort_unless($this->access->canEdit(auth()->user()), 403);

        $data = $request->validate(['status' => 'required|in:pending,confirmed,shipping,completed,cancelled']);
        $this->orders->update($order, $data);

        return redirect()->route('admin.orders.index')->with('success', 'Cập nhật đơn hàng thành công!');
    }

    public function destroy(Order $order)
    {
        abort_unless($this->access->canDelete(auth()->user()), 403);

        $this->orders->delete($order);

        return back()->with('success', 'Xóa đơn hàng thành công!');
    }

    public function store(Request $request)
    {
        $lock = Cache::lock('order_lock_' . auth()->id(), 10); // khóa 10 giây

        if (!$lock->get()) {
            return $this->redirectToBuy($request)->withInput()->withErrors([
                'error' => 'Đơn hàng đang được xử lý, vui lòng đợi...',
            ]);
        }

        $validator = Validator::make($request->all(), [
            'receive_user'    => ['required', 'max:255', new ReceiverFullName()],
            'product_id'      => 'required|exists:products,id',
            'qty'             => 'nullable|integer|min:1',
            'child_ids'       => 'nullable|array',
            'child_qtys'      => 'nullable|array',
            'mode'            => 'nullable|in:custom,custom_co_che',
            'receive_phone'   => 'required|max:20',
            'province_id'     => 'required',
            'district_id'     => 'required',
            'ward_id'         => 'required',
            'receive_address' => 'required|max:500',
            'note'            => 'nullable|max:500',
            'confirmed'       => 'required|accepted',
        ], [
            'receive_user.required'    => 'Vui long nhap ten nguoi nhan.',
            'receive_phone.required'   => 'Vui long nhap so dien thoai.',
            'province_id.required'     => 'Vui long chon tinh/thanh.',
            'district_id.required'     => 'Vui long chon quan/huyen.',
            'ward_id.required'         => 'Vui long chon phuong/xa.',
            'receive_address.required' => 'Vui long nhap dia chi cu the.',
            'confirmed.required'       => 'Ban can tich xac nhan truoc khi dat mua.',
            'confirmed.accepted'       => 'Ban can tich xac nhan truoc khi dat mua.',
        ]);

        if ($validator->fails()) {
            $lock->release();

            return $this->redirectToBuy($request)->withErrors($validator)->withInput();
        }

        $product = Product::findOrFail($request->product_id);

        try {
            // Nếu là Mua tự chọn (có child_ids)
            $order = $request->filled('child_ids')
                ? $this->service->muaTuChon($request, $product)
                : $this->service->muaGoi($request, $product);

            $lock->release();

            return redirect()->route('admin.thank-you', ['orderId' => $order->ma_don_hang])
                ->with('success', 'Đặt mua thành công! Mã đơn: ' . $order->ma_don_hang);
        } catch (\Exception $e) {
            $lock->release();

            if ((int) $e->getCode() === 402) {
                // Ví không đủ → chuyển sang thanh toán PayOS (request đã validate ở trên)
                return app(CheckoutController::class)->redirectToPayment($request);
            }

            return $this->redirectToBuy($request)->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /** Tính lại VAT 8% cho toàn bộ đơn */
    public function recalculateVat()
    {
        foreach ($this->orders->all() as $order) {
            $order->price_vat = ($order->total_price_hoan_tien * 8) / 100;
            $order->save();
        }
    }

    protected function redirectToBuy(Request $request)
    {
        $productId = $request->input('product_id');

        if (!$productId) {
            return redirect()->route('admin.shop.index');
        }

        $params = ['product' => $productId];

        if ($request->filled('mode') || $request->filled('child_ids')) {
            $params['mode'] = in_array($request->input('mode'), ['custom', 'custom_co_che'], true)
                ? $request->input('mode')
                : 'custom';
        }

        return redirect()->route('admin.shop.buy', $params);
    }
}
