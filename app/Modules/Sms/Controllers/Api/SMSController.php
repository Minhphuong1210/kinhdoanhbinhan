<?php

namespace App\Modules\Sms\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Sms\Services\ESMSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SMSController extends Controller
{
    private const OTP_MINUTES = 2;

    public function __construct(private readonly ESMSService $sms)
    {
    }

    public function test()
    {
        return view('Sms::Admin.test');
    }

    public function sendOtp(Request $request)
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'regex:/^(0|\+84)[0-9]{9}$/'],
        ]);

        $otp = random_int(100000, 999999);

        Cache::put('otp_' . $data['phone'], $otp, now()->addMinutes(self::OTP_MINUTES));

        $result = $this->sms->sendOTP($data['phone'], $otp);

        if ($result && isset($result['CodeResult']) && $result['CodeResult'] == 100) {
            return response()->json(['message' => 'Gửi thành công']);
        }

        return response()->json(['message' => 'Gửi thất bại'], 500);
    }

    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'phone' => ['required', 'string'],
            'otp' => ['required', 'digits:6'],
        ]);

        $cachedOtp = Cache::get('otp_' . $data['phone']);

        if ($cachedOtp && hash_equals((string) $cachedOtp, $data['otp'])) {
            Cache::forget('otp_' . $data['phone']);

            return response()->json(['message' => 'Xác minh thành công', 'status' => true]);
        }

        return response()->json(['message' => 'Mã OTP không chính xác hoặc đã hết hạn', 'status' => false], 400);
    }
}
