<?php

namespace App\Modules\Payment\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Payment\Models\LichSuPayos;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * POST /payos/create-payment-link
     * Tạo link thanh toán PayOS rồi redirect.
     */
    public function createPaymentLink(Request $request)
    {

//        dd($request->all());


        $request->validate([
            'product_id'      => 'required|exists:products,id',
            'qty'             => 'nullable|integer|min:1',
            'child_ids'       => 'nullable|array',
            'child_qtys'      => 'nullable|array',


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

                        'em',
                        'ông',

                        'bà',
                        'ba',
                        'cô',

                        'chú',
                        'bác',

                        'dì',

                        'mẹ',

                        'bố',

                        'cha',
                        'thím',

                        'mợ',

                        'cậu',

                        'Cô',
                        'Chị',
                        'Anh'
                    ];

                    if (in_array($lowerWords[0], $invalidFirstWords)) {
                        return $fail('Vui lòng nhập đầy đủ họ và tên người nhận, không nhập cách xưng hô.');
                    }

                    // Các cụm từ không hợp lệ
                    $invalidPhrases = [
                        'cậu mợ',

                        'cô chú',

                        'ông bà',

                        'ba mẹ',
                        'ba me',
                        'bố mẹ',

                        'cha mẹ',
                       
                    ];

                    if (in_array($lowerValue, $invalidPhrases)) {
                        return $fail('Vui lòng nhập đầy đủ họ và tên người nhận.');
                    }

                    // Chỉ cho phép chữ cái và khoảng trắng
                    if (!preg_match('/^[\p{L}\s]+$/u', $value)) {
                        return $fail('Tên người nhận chỉ được chứa chữ cái.');
                    }

//                    // Mỗi từ phải có ít nhất 2 ký tự
//                    foreach ($words as $word) {
//                        if (mb_strlen($word) < 2) {
//                            return $fail('Vui lòng nhập đúng họ và tên.');
//                        }
//                    }

//                    // Giới hạn số từ (ví dụ tối đa 6 từ)
//                    if (count($words) > 6) {
//                        return $fail('Họ và tên không hợp lệ.');
//                    }
                }
            ],



            'receive_phone'   => 'required|max:20',
            'province_id'     => 'required',
            'district_id'     => 'required',
            'ward_id'         => 'required',
            'receive_address' => 'required|max:500',
            'note'            => 'nullable|max:500',
            'confirmed'       => 'required|accepted',
        ], [
            'receive_user.required'    => 'Vui lòng nhập tên người nhận.',
            'receive_phone.required'   => 'Vui lòng nhập số điện thoại.',
            'province_id.required'     => 'Vui lòng chọn tỉnh/thành.',
            'district_id.required'     => 'Vui lòng chọn quận/huyện.',
            'ward_id.required'         => 'Vui lòng chọn phường/xã.',
            'receive_address.required' => 'Vui lòng nhập địa chỉ cụ thể.',
            'confirmed.required'       => 'Bạn cần tích xác nhận trước khi đặt mua.',
            'confirmed.accepted'       => 'Bạn cần tích xác nhận trước khi đặt mua.',
        ]);



        if (! empty($request->child_ids)) {

            foreach ($request->child_ids as $productId) {
                $product = \App\Models\Product::where('id', $productId)->first();

                $childIds  = $request->child_ids ?? [];
                $childQtys = $request->child_qtys ?? [];

                $selections = [];
                $totalPrice = 0;

                foreach ($childIds as $index => $id) {
                    $qty = (int) ($childQtys[$index] ?? 0);
                    if ($qty > 0) {
                        $child = \App\Models\Product::find($id);
                        if ($child) {
                            $selections[]  = [
                                'product' => $child,
                                'qty'     => $qty,
                            ];
                            $totalPrice += ($child->price * $qty);
                        }
                    }
                }

                if ($totalPrice < 1574000) {
                    return back()->with('error', 'Vui lòng chọn đúng giá từ 1574000 ');

                }

            }

        }



        $goiDichVuId = $request->input('goiDichVuId');
        $price       = intval(preg_replace('/[^0-9]/', '', $request->input('total_price_hidden', '0')));

        $YOUR_DOMAIN = rtrim(config('app.url'), '/') . '/payos';

        // ✅ Order code chống trùng: 6 số cuối Unix timestamp + 3 số random
        $orderCode = intval(substr(strval(time()), -6) . str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT));


        $value = trim(preg_replace('/\s+/', ' ',  $request->input('receive_user', '')));

        // Ít nhất 2 từ
        $words = explode(' ', $value);

        $lowerWords = array_map('mb_strtolower', $words);


        $paymentData = [
            'orderCode'   => $orderCode,
            'amount'      => $price,
            'description' => end($lowerWords) .' ' . 'tt',
            'buyerName'   => auth()->user()->name ?? '',
            'returnUrl'   => $YOUR_DOMAIN . '/succes',
            'cancelUrl'   => $YOUR_DOMAIN . '/cancel',
            'expiredAt'   => time() + (10 * 60),
        ];
        try {
        $response = $this->createLink($paymentData);

        LichSuPayos::create([
            'admin_id'            => auth()->id(),
            'ma_don'              => $orderCode,
            'loai_don'            => 'Nạp tiền',
            'hinh_thuc_thanh_toan'=> 'QR Pay',
            'so_tien'             => $price,
            'trang_thai'          => 'Đang xử lý',
            'cancel'              => 0,
            'link'                => $response['checkoutUrl'] ?? null,
            'description'         => $response['description'] ?? 'Nap tien',
            'product_json' => json_encode($request->except('_token')) ?? null,
        ]);

        return redirect($response['checkoutUrl']);

        } catch (\Throwable $th) {
            if ($th->getCode() === 503) {
                return back()->with('error', 'Hệ thống PayOS đang gián đoạn. Xin lỗi vì sự bất tiện.');
            }
            return back()->with('error', "Lỗi: {$th->getMessage()} | Code: {$th->getCode()}");
        }
    }

    // ----------------------------------------------------------------
    //  Internal helpers
    // ----------------------------------------------------------------

    protected function createLink(array $paymentData): array
    {
        $clientId    = env('PAYOS_CLIENT_ID');
        $apiKey      = env('PAYOS_API_KEY');
        $checksumKey = env('PAYOS_CHECKSUM_KEY');

        $required = ['orderCode', 'amount', 'description', 'returnUrl', 'cancelUrl'];
        foreach ($required as $k) {
            if (empty($paymentData[$k])) {
                throw new \Exception("Thiếu tham số bắt buộc: {$k}", 400);
            }
        }

        $signature = static::createSignatureOfPaymentRequest($checksumKey, $paymentData);
        $payload   = array_merge($paymentData, ['signature' => $signature]);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://api-merchant.payos.vn/v2/payment-requests',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                "x-client-id: {$clientId}",
                "x-api-key: {$apiKey}",
            ],
            CURLOPT_TIMEOUT        => 15,
        ]);
        $res = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            throw new \Exception("CURL error: {$err}", 500);
        }

        $json = json_decode($res, true);

        if (!isset($json['code'], $json['data'], $json['signature'])) {
            throw new \Exception('Response không hợp lệ từ PayOS.', 500);
        }

        if ($json['code'] === '00') {
            $sigVerify = static::createSignatureFromObj($checksumKey, $json['data']);
            if ($sigVerify !== $json['signature']) {
                throw new \Exception('Dữ liệu không toàn vẹn (sai chữ ký).', 500);
            }
            return $json['data'];
        }

        throw new \Exception($json['desc'] ?? 'Unknown error from PayOS', (int)($json['code'] ?? 500));
    }

    protected static function createSignatureFromObj(string $checksumKey, array $obj): string
    {
        ksort($obj);
        $parts = [];
        foreach ($obj as $k => $v) {
            if ($v === null || $v === 'undefined' || $v === 'null') {
                $v = '';
            }
            $parts[] = "{$k}={$v}";
        }
        return hash_hmac('sha256', implode('&', $parts), $checksumKey);
    }

    protected static function createSignatureOfPaymentRequest(string $checksumKey, array $obj): string
    {
        $dataStr = "amount={$obj['amount']}&cancelUrl={$obj['cancelUrl']}&description={$obj['description']}&orderCode={$obj['orderCode']}&returnUrl={$obj['returnUrl']}";
        return hash_hmac('sha256', $dataStr, $checksumKey);
    }
}
