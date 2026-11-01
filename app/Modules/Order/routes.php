<?php

use App\Modules\Order\Controllers\Api\OrderApiController;
use App\Modules\Order\Controllers\Admin\OrderController;
use App\Modules\Order\Controllers\Admin\ShopController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('/orders', [OrderApiController::class, 'index']);
    Route::get('/orders/theo-thoi-gian-mua', [OrderApiController::class, 'theoThoiGianMua']);
    Route::get('/orders/buy-info/{product}', [OrderApiController::class, 'buyInfo']);
    Route::post('/orders/preview-tu-chon', [OrderApiController::class, 'previewTuChon']);
    Route::post('/orders/mua-combo', [OrderApiController::class, 'muaCombo'])->middleware('throttle:30,1');
    Route::post('/orders/mua-tu-chon', [OrderApiController::class, 'muaTuChon'])->middleware('throttle:30,1');
    Route::get('/orders/{order}', [OrderApiController::class, 'show']);
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web'])->group(function () {
    Route::resource('orders', OrderController::class)->except(['create']);
    Route::get('orders-export', [OrderController::class, 'exportExcel'])->name('orders.export');
});

// Gói sản phẩm — tất cả user đều xem được
Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', 'admin', 'profile.complete'])->group(function () {
    Route::get('shop', [ShopController::class, 'index'])->name('shop.index');
    Route::get('shop/buy/{product}', [ShopController::class, 'buy'])->name('shop.buy');
    Route::post('shop/confirm', [ShopController::class, 'confirm'])->name('shop.confirm');
    Route::get('thank-you/{orderId}', [ShopController::class, 'thankYou'])->name('thank-you');
});

Route::middleware(['web', 'auth', 'admin', 'permission:setting.edit'])->get('/delivery_fee', [OrderController::class, 'recalculateVat']);
