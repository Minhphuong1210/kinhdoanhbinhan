<?php 

use Illuminate\Support\Facades\Route;
use App\Modules\Sms\Http\Controller\SMSController;

Route::post('/send-otp', [SMSController::class, 'sendOtp'])->name('admin.sms.send');
Route::post('/verify-otp', [SMSController::class, 'verifyOtp'])->name('admin.sms.verify');
Route::get('/sms-test', [SMSController::class, 'test'])->name('admin.sms.test');
