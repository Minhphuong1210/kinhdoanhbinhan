<?php

use App\Modules\Order\Controllers\Admin\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web'])->group(function () {
    Route::resource('orders', OrderController::class)->except(['create']);
    Route::get('orders-export', [OrderController::class, 'exportExcel'])->name('orders.export');
});
