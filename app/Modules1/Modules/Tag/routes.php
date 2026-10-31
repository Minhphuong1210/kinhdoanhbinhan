<?php

use App\Modules\Tag\Controllers\Admin\TagController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web'])->group(function () {
    Route::resource('tags', TagController::class)->except(['show']);
});
