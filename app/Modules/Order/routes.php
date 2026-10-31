<?php

use App\Modules\Order\Controllers\Api\OrderApiController;
use App\Modules\Order\Controllers\Admin\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('/orders', [OrderApiController::class, 'index']);
    Route::get('/orders/theo-thoi-gian-mua', [OrderApiController::class, 'theoThoiGianMua']);
    Route::get('/orders/buy-info/{product}', [OrderApiController::class, 'buyInfo']);
    Route::post('/orders/preview-tu-chon', [OrderApiController::class, 'previewTuChon']);
    Route::post('/orders/mua-combo', [OrderApiController::class, 'muaCombo']);
    Route::post('/orders/mua-tu-chon', [OrderApiController::class, 'muaTuChon']);
    Route::get('/orders/{order}', [OrderApiController::class, 'show']);
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web'])->group(function () {
    Route::resource('orders', OrderController::class)->except(['create']);
    Route::get('orders-export', [OrderController::class, 'exportExcel'])->name('orders.export');
});
