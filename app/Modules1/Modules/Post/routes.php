<?php

use App\Modules\Post\Controllers\Admin\PostController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web'])->group(function () {
    Route::resource('posts', PostController::class)->except(['show']);


    Route::post(
        'post/noi_dung_chi_tiet/upload-image',
        [PostController::class, 'image']
    )->name('post.upload-image');

});
