<?php

use App\Modules\Shipping\Controllers\Admin\PancakeSyncController;
use App\Modules\Shipping\Controllers\Admin\ShippingController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/shipping')
    ->name('admin.shipping.')
    ->middleware(['web', 'auth', 'admin', 'permission:setting.edit'])
    ->group(function () {
        Route::get('settings', [ShippingController::class, 'settings'])->name('settings');
        Route::post('settings', [ShippingController::class, 'saveSettings'])->name('settings.save');
    });

// Đồng bộ Pancake: chỉ người có quyền sửa cài đặt mới chạy được
Route::middleware(['web', 'auth', 'admin', 'permission:setting.edit'])->group(function () {
    Route::get('/test-pancake-shops', [PancakeSyncController::class, 'shops']);
    Route::get('/test-pancake-warehouses', [PancakeSyncController::class, 'warehouses']);
    Route::get('/test-pancake-products', [PancakeSyncController::class, 'products']);
    Route::get('/test-pancake-provinces', [PancakeSyncController::class, 'provinces']);
    Route::get('/test-pancake-districts', [PancakeSyncController::class, 'districts']);
    Route::get('/test-pancake-communes', [PancakeSyncController::class, 'communes']);
    Route::get('/map-pancake-provinces', [PancakeSyncController::class, 'mapProvinces']);
});
