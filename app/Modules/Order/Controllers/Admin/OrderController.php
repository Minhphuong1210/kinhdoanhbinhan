<?php

namespace App\Modules\Order\Controllers\Admin;

use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\OrderService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
class OrderController extends Controller
{
    protected $service;

    public function __construct(OrderService $service)
    {
        $this->service = $service;
    }
    public function index(Request $request)
    {
        $user    = auth()->user();
        $isAdmin = $user->is_admin || $user->hasPermission('order.view');

        $query = Order::with('user');
        $products = \App\Models\Product::where('active', 1)->get(['id', 'name']);

        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        } else {
            if ($search = $request->search) {
                $query->where(function($q) use ($search) {
                    $q->where('ma_don_hang', 'like', "%$search%")
                        ->orWhere('name', 'like', "%$search%");
                });
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('product_id')) {
                $query->whereHas('details', function($q) use ($request) {
                    $q->where('product_id', $request->product_id);
                });
            }
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
        }

        $orders = $query->latest()->paginate(20)->withQueryString();
        return view('Order::Admin.index', compact('orders', 'isAdmin', 'products'));
    }

    public function exportExcel(Request $request)
    {
        $user    = auth()->user();
        $isAdmin = $user->is_admin || $user->hasPermission('order.view');

        $query = Order::with(['user', 'details', 'province', 'district', 'ward']);
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        } else {
            if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
            if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);
            if ($request->filled('status'))    $query->where('status', $request->status);
            if ($request->filled('product_id')) {
                $query->whereHas('details', function($q) use ($request) {
                    $q->where('product_id', $request->product_id);
                });
            }
        }

        $orders   = $query->get();
        $from     = $request->date_from ? '_' . $request->date_from . '_' . ($request->date_to ?: now()->toDateString()) : '';
        $filename = 'Don-hang' . $from . '.xlsx';

        return Excel::download(new OrdersExport($orders), $filename);
    }

    public function show(Order $order)
    {
        $order->load('user', 'details.product');
        return view('Order::Admin.show', compact('order'));
    }

    public function edit(Order $order)
    {
        return view('Order::Admin.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate(['status' => 'required|in:pending,confirmed,shipping,completed,cancelled']);
        $order->update($data);
        return redirect()->route('admin.orders.index')->with('success', 'Cập nhật đơn hàng thành công!');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return back()->with('success', 'Xóa đơn hàng thành công!');
    }

    public function store(Request $request)
    {

        $min_price_buy = 1500000;

        $user = auth()->user();
        $lockKey = 'order_lock_' . $user->id;

        $lock = Cache::lock($lockKey, 10); // khóa 10 giây

        if (! $lock->get()) {
            return $this->redirectToBuy($request)->withInput()->withErrors([
                'error' => 'Đơn hàng đang được xử lý, vui lòng đợi...'
            ]);
        }


        $validator = Validator::make($request->all(), [
            'receive_user' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) {

                    // Chuẩn hóa khoảng trắng
                    $value = trim(preg_replace('/\s+/', ' ', $value));

                    // Ít nhất 2 từ
                    $words = explode(' ', $value);

                    if (count($words) < 2) {
                        return $fail('Vui lòng nhập đầy đủ họ và tên.');
                    }

                    // Chuyển về chữ thường
                    $lowerValue = mb_strtolower($value);
                    $lowerWords = array_map('mb_strtolower', $words);

                    // Danh sách từ xưng hô không được đứng đầu
                    $invalidFirstWords = [
                        'anh',
                        'chị',
                        'chi',
                        'em',
                        'ông',
                        'ong',
                        'bà',
                        'ba',
                        'cô',
                        'co',
                        'chú',
                        'chu',
                        'bác',
                        'bac',
                        'dì',
                        'di',
                        'mẹ',
                        'me',
                        'bố',
                        'bo',
                        'cha',
                        'thím',
                        'thim',
                        'mợ',
                        'mo',
                        'cậu',
                        'cau',
                    ];

                    if (in_array($lowerWords[0], $invalidFirstWords)) {
                        return $fail('Vui lòng nhập đầy đủ họ và tên người nhận, không nhập cách xưng hô.');
                    }

                    // Các cụm từ không hợp lệ
                    $invalidPhrases = [
                        'cậu mợ',
                        'cau mo',
                        'cô chú',
                        'co chu',
                        'ông bà',
                        'ong ba',
                        'ba mẹ',
                        'ba me',
                        'bố mẹ',
                        'bo me',
                        'cha mẹ',
                        'cha me',
                    ];

                    if (in_array($lowerValue, $invalidPhrases)) {
                        return $fail('Vui lòng nhập đầy đủ họ và tên người nhận.');
                    }

                    // Chỉ cho phép chữ cái và khoảng trắng
                    if (!preg_match('/^[\p{L}\s]+$/u', $value)) {
                        return $fail('Tên người nhận chỉ được chứa chữ cái.');
                    }

                }
            ],
            'product_id'     => 'required|exists:products,id',
            'qty'            => 'nullable|integer|min:1',
            'child_ids'      => 'nullable|array',
            'child_qtys'     => 'nullable|array',
            'mode'           => 'nullable|in:custom,custom_co_che',
            'receive_phone'  => 'required|max:20',
            'province_id'    => 'required',
            'district_id'    => 'required',
            'ward_id'        => 'required',
            'receive_address'=> 'required|max:500',
            'note'           => 'nullable|max:500',
            'confirmed'      => 'required|accepted',
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

            return $this->redirectToBuy($request)
                ->withErrors($validator)
                ->withInput();
        }

        $product = \App\Models\Product::findOrFail($request->product_id);
//        $qty = max(1, (int) $request->qty);
//        [$total, $totalHoan]= $this->service->tinhTongTien($product,$qty);
//        if($total<$min_price_buy ){
//            return redirect()->back()
//                ->with('error', 'Số tiền không đủ mua hàng');
//        }


//        dd($request->all());

        try {


            // Nếu là Mua tự chọn (có child_ids)
            if ($request->filled('child_ids')) {
                $order = $this->service->muaTuChon($request, $product);
            } else {
                $order = $this->service->muaGoi($request, $product);
            }

            $lock->release();

            return redirect()->route('admin.thank-you', ['orderId' => $order->ma_don_hang])
                ->with('success', 'Đặt mua thành công! Mã đơn: ' . $order->ma_don_hang);

        } catch (\Exception $e) {
            $lock->release();

            if ((int)$e->getCode() === 402) {
                // Ví không đủ → chuyển sang thanh toán PayOS (request đã validate ở trên)
                return app(\App\Modules\Payment\Controllers\Admin\CheckoutController::class)
                    ->redirectToPayment($request);
            }

            return $this->redirectToBuy($request)
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    protected function redirectToBuy(Request $request)
    {
        $productId = $request->input('product_id');
        $mode = in_array($request->input('mode'), ['custom', 'custom_co_che'], true)
            ? $request->input('mode')
            : 'custom';

        if ($productId) {
            $params = ['product' => $productId];

            if ($request->filled('mode') || $request->filled('child_ids')) {
                $params['mode'] = $mode;
            }

            return redirect()->route('admin.shop.buy', $params);
        }

        return redirect()->route('admin.shop.index');
    }
}
