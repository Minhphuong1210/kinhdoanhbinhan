<?php

use App\Modules\Product\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web'])->group(function () {
    Route::resource('products', ProductController::class)->except(['show']);

    Route::post(
        'product/noi_dung_chi_tiet/upload-image',
        [ProductController::class, 'image']
    )->name('products.upload-image');


});

