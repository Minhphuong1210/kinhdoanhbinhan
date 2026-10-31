<?php

use App\Modules\GioiThieu\Controllers\Admin\GioiThieuController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web', 'permission:content.manage'])->group(function () {
    Route::resource('gioi-thieus', GioiThieuController::class)->except(['show']);
});
