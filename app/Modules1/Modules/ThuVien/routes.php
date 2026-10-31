<?php

use App\Modules\ThuVien\Controllers\Admin\ThuVienController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web'])->group(function () {
    Route::resource('thu-viens', ThuVienController::class)->except(['show']);
});
