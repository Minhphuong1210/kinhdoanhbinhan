<?php

use App\Modules\Category\Controllers\Admin\CategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web'])->group(function () {
    Route::resource('categories', CategoryController::class)->except(['show']);
});
