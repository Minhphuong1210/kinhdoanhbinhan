<?php


namespace App\Modules\Sms\Http\Controller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Sms\Services\ESMSService;
use Illuminate\Support\Facades\Cache;

class SMSController extends Controller
{
    protected $sms;

    public function __construct(ESMSService $sms)
    {
        $this->sms = $sms;
    }

    public function test()
    {
        return view('Sms::Admin.test');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required'
        ]);

        $otp = rand(100000, 999999);

        Cache::put('otp_' . $request->phone, $otp, now()->addMinutes(2));

        $result = $this->sms->sendOTP($request->phone, $otp);

        if ($result && isset($result['CodeResult']) && $result['CodeResult'] == 100) {
            return response()->json(['message' => 'Gửi thành công']);
        }

        return response()->json(['message' => 'Gửi thất bại'], 500);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'otp' => 'required'
        ]);

        $cachedOtp = Cache::get('otp_' . $request->phone);

        if ($cachedOtp && $cachedOtp == $request->otp) {
            Cache::forget('otp_' . $request->phone);
            return response()->json(['message' => 'Xác minh thành công', 'status' => true]);
        }

        return response()->json(['message' => 'Mã OTP không chính xác hoặc đã hết hạn', 'status' => false], 400);
    }
}