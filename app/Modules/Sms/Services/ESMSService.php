<?php 

namespace App\Modules\Sms\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Modules\Sms\Models\SMSLogs;
use Illuminate\Support\Str;

class ESMSService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('sms.esms');
    }

    public function sendOTP($phone, $otp)
    {
        $content = "$otp la ma xac minh dang ky Baotrixemay cua ban";
        $requestId = Str::uuid();




        try {
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->config['url'], [
                'ApiKey' => $this->config['api_key'],
                'SecretKey' => $this->config['secret'],
                'Phone' => $phone,
                'Content' => $content,
                'SmsType' => 2,
                'Brandname' => $this->config['brandname'],
                'IsUnicode' => 0,
                'RequestId' => $requestId,
            ]);

            $result = $response->json();

            SMSLogs::create([
                'phone' => $phone,
                'content' => $content,
                'brandname' => $this->config['brandname'],
                'request_id' => $requestId,
                'status' => (isset($result['CodeResult']) && $result['CodeResult'] == 100) ? 'success' : 'fail',
                'response' => json_encode($result)
            ]);

            return $result;

        } catch (\Exception $e) {
            SMSLogs::create([
                'phone' => $phone,
                'content' => $content,
                'brandname' => $this->config['brandname'],
                'request_id' => $requestId,
                'status' => 'fail',
                'response' => $e->getMessage()
            ]);

            Log::error('SMS ERROR', ['error' => $e->getMessage()]);

            return false;
        }
    }
}