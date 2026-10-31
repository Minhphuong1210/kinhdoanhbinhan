<?php

use App\Modules\Product\Controllers\Api\ProductApiController;
use App\Modules\Product\Controllers\Admin\ProductController;
use App\Modules\Product\Controllers\Admin\CoCheHoanPhiController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware('api')->group(function () {
    Route::get('/categories', [ProductApiController::class, 'categories']);
    Route::get('/categories/{category}/products', [ProductApiController::class, 'categoryProducts']);
    Route::get('/products', [ProductApiController::class, 'products']);
    Route::get('/products/{product}/san-pham-tu-chon', [ProductApiController::class, 'sanPhamTuChon'])->whereNumber('product');
    Route::get('/products/{slug}', [ProductApiController::class, 'productDetail']);
    Route::get('/news-categories', [ProductApiController::class, 'newsCategories']);
    Route::get('/news', [ProductApiController::class, 'news']);
    Route::get('/news/{slug}', [ProductApiController::class, 'newsDetail']);

Route::get('/gioi-thieu-chung-toi', [ProductApiController::class, 'gioiThieuChungToi']);

});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web'])->group(function () {
    Route::resource('co-che-hoan-phi', CoCheHoanPhiController::class)->except(['show']);

    Route::resource('products', ProductController::class)->except(['show']);

    Route::post(
        'product/noi_dung_chi_tiet/upload-image',
        [ProductController::class, 'image']
    )->name('products.upload-image')->middleware('permission:product.edit');


});

