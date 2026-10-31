<?php

use App\Modules\Promotion\Controllers\Admin\PromotionController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web'])->group(function () {

    Route::resource('promotions', PromotionController::class)->except(['show']);

    // Quản lý sản phẩm trong sự kiện
    Route::post('promotions/{promotion}/add-product', [PromotionController::class, 'addProduct'])
        ->name('promotions.add-product');
    Route::delete('promotions/{promotion}/remove-product/{productPromotion}', [PromotionController::class, 'removeProduct'])
        ->name('promotions.remove-product');

    // Quản lý quà tặng
    Route::post('promotions/product-promotion/{productPromotion}/add-gift', [PromotionController::class, 'addGift'])
        ->name('promotions.add-gift');
    Route::delete('promotions/gift/{gift}', [PromotionController::class, 'removeGift'])
        ->name('promotions.remove-gift');
});
