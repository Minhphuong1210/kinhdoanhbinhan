<?php

use App\Modules\DangKyHTX\Controllers\Admin\DangKyHTXController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web'])->group(function () {
    Route::get('dang-ky-htxs', [DangKyHTXController::class, 'index'])->name('dang-ky-htxs.index');
    Route::get('dang-ky-htxs/{dangKyHTX}', [DangKyHTXController::class, 'show'])->name('dang-ky-htxs.show');
    Route::put('dang-ky-htxs/{dangKyHTX}', [DangKyHTXController::class, 'update'])->name('dang-ky-htxs.update');
    Route::get('dang-ky-htxs/{dangKyHTX}/pdf', [DangKyHTXController::class, 'exportPdf'])->name('dang-ky-htxs.pdf');
});
