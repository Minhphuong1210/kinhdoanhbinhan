<?php

use App\Modules\Finance\Controllers\Admin\LichSuGiaoDichController;
use App\Modules\Finance\Controllers\Admin\YeuCauRutTienController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web'])->group(function () {
//    Route::get('lich-su-giao-dich', [LichSuGiaoDichController::class, 'index'])->name('lich-su-giao-dich.index');
    Route::get('lich-su-giao-dich', [LichSuGiaoDichController::class, 'index'])->name('lich-su-giao-dich.index')->middleware('permission:finance.view');
    Route::delete('lich-su-giao-dich/{lichSuGiaoDich}', [LichSuGiaoDichController::class, 'destroy'])->name('lich-su-giao-dich.destroy');
    Route::get('yeu-cau-rut-tien/export-excel', [YeuCauRutTienController::class , 'exportExcel'])->name('yeu-cau-rut-tien.export-excel');
    Route::get('yeu-cau-rut-tien', [YeuCauRutTienController::class, 'index'])->name('yeu-cau-rut-tien.index');
    Route::put('yeu-cau-rut-tien/{yeuCauRutTien}', [YeuCauRutTienController::class, 'update'])->name('yeu-cau-rut-tien.update');
    Route::delete('yeu-cau-rut-tien/{yeuCauRutTien}', [YeuCauRutTienController::class, 'destroy'])->name('yeu-cau-rut-tien.destroy');
});
