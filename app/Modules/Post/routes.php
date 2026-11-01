<?php

use App\Modules\Post\Controllers\Admin\PostController;
use App\Modules\Post\Controllers\Api\NewsApiController;
use App\Modules\Post\Controllers\Frontend\NewsController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web'])->group(function () {
    Route::resource('posts', PostController::class)->except(['show']);


    Route::post(
        'post/noi_dung_chi_tiet/upload-image',
        [PostController::class, 'image']
    )->name('post.upload-image');

});

Route::prefix('api')->middleware('api')->group(function () {
    Route::get('/news-categories', [NewsApiController::class, 'newsCategories']);
    Route::get('/news', [NewsApiController::class, 'news']);
    Route::get('/news/{slug}', [NewsApiController::class, 'newsDetail']);
});

Route::middleware(['web'])->name('frontend.')->group(function () {
    Route::get('/tin-tuc', [NewsController::class, 'index'])->name('news.index');
    Route::get('/tin-tuc/{slug}', [NewsController::class, 'show'])->name('news.show');
});
