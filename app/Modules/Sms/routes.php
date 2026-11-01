<?php

use App\Modules\Sms\Controllers\Api\SMSController;
use Illuminate\Support\Facades\Route;

Route::post('/send-otp', [SMSController::class, 'sendOtp'])->name('admin.sms.send')->middleware('throttle:sensitive');
Route::post('/verify-otp', [SMSController::class, 'verifyOtp'])->name('admin.sms.verify')->middleware('throttle:login');
Route::get('/sms-test', [SMSController::class, 'test'])->name('admin.sms.test')->middleware(['web', 'auth', 'admin', 'permission:setting.edit']);
