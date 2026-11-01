<?php

use App\Modules\Payment\Controllers\Admin\CheckoutController;
use App\Modules\Payment\Controllers\Admin\PaymentHistoryController;
use App\Modules\Payment\Controllers\Frontend\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

// ============================================================
//  PayOS callbacks — PUBLIC (PayOS redirect về, không có session auth)
// ============================================================
Route::prefix('payos')->middleware(['web', 'throttle:30,1'])->group(function () {
    // Tạo link nạp tiền — cần đăng nhập (user đã đăng nhập khi bấm nạp tiền)
    Route::post('create-payment-link', [CheckoutController::class, 'createPaymentLink'])
        ->middleware(['auth', 'admin'])  // max 5 lần/phút
        ->name('payos.create-payment-link');

    // Callback PayOS: khách huỷ / thành công — KHÔNG yêu cầu auth (redirect từ PayOS)
    Route::get('cancel', [PaymentHistoryController::class, 'cancelPayment'])
        ->name('payos.cancel');

    Route::get('succes', [PaymentHistoryController::class, 'succesPayment'])
        ->name('payos.succes');
});

// ============================================================
//  Admin routes — cần auth + admin
// ============================================================
Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', 'admin'])->group(function () {
    // Danh sách lịch sử PayOS
    Route::get('payment-history', [PaymentHistoryController::class, 'index'])
        ->name('payment-history.index');

    // Đồng bộ thủ công trạng thái từ PayOS API
    Route::get('payment-history/cap-nhat', [PaymentHistoryController::class, 'capNhatTrangThai'])
        ->name('payment-history.cap-nhat')
        ->middleware('throttle:5,1');

    // Form nạp tiền (hiển thị trong admin panel)
//    Route::get('nap-tien', function () {
//        return view('Payment::Admin.payment-history.create');
//    })->name('nap-tien');
});

Route::post('payos-webhook', [PaymentWebhookController::class, 'webhook'])->name('webhook')->middleware('throttle:120,1');


//Route::get('/test-payos-order/{id}', function ($id) {
//
//    $payos = \App\Modules\Payment\Models\LichSuPayos::findOrFail($id);
//
//    $controller = app(\App\Modules\Payment\Controllers\Frontend\PaymentWebhookController::class);
//
//    $controller->processOrderFromPayos($payos);
//
//    return [
//        'success' => true,
//        'payment_id' => $payos->fresh()->payment_id
//    ];
//});