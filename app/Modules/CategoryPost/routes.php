<?php

use App\Modules\CategoryPost\Controllers\Admin\CategoryPostController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web', 'permission:post.edit'])->group(function () {
    Route::resource('category-posts', CategoryPostController::class)->except(['show']);
});
