<?php

use App\Modules\Leader\Controllers\Admin\LeaderController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web', 'permission:content.manage'])->group(function () {
    Route::resource('leaders', LeaderController::class)->except(['show']);
});
