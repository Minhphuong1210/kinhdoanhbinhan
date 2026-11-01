<?php

use App\Modules\Finance\Controllers\Admin\LichSuGiaoDichController;
use App\Modules\Finance\Controllers\Admin\UserFinanceController;
use App\Modules\Finance\Controllers\Admin\YeuCauRutTienController;
use App\Modules\Finance\Controllers\Api\ActivityApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('/activity-history', [ActivityApiController::class, 'index']);
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web'])->group(function () {
    Route::get('lich-su-giao-dich', [LichSuGiaoDichController::class, 'index'])->name('lich-su-giao-dich.index')->middleware('permission:finance.view');
    Route::delete('lich-su-giao-dich/{lichSuGiaoDich}', [LichSuGiaoDichController::class, 'destroy'])->name('lich-su-giao-dich.destroy')->middleware('permission:finance.approve');
    Route::get('yeu-cau-rut-tien/export-excel', [YeuCauRutTienController::class, 'exportExcel'])->name('yeu-cau-rut-tien.export-excel')->middleware('permission:finance.view');
    Route::get('yeu-cau-rut-tien', [YeuCauRutTienController::class, 'index'])->name('yeu-cau-rut-tien.index')->middleware('permission:finance.view');
    Route::put('yeu-cau-rut-tien/{yeuCauRutTien}', [YeuCauRutTienController::class, 'update'])->name('yeu-cau-rut-tien.update')->middleware('permission:finance.approve');
    Route::delete('yeu-cau-rut-tien/{yeuCauRutTien}', [YeuCauRutTienController::class, 'destroy'])->name('yeu-cau-rut-tien.destroy')->middleware('permission:finance.approve');
});

// Tài chính cá nhân
Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', 'admin', 'profile.complete'])->group(function () {
    Route::get('my-transactions', [UserFinanceController::class, 'transactions'])->name('my-transactions');
    Route::get('my-withdrawals', [UserFinanceController::class, 'withdrawals'])->name('my-withdrawals');
    Route::post('my-withdrawals', [UserFinanceController::class, 'requestWithdrawal'])->name('my-withdrawals.store')->middleware('throttle:pin');
    Route::get('my-income', [UserFinanceController::class, 'income'])->name('my-income');
    Route::get('chuyen-diem', [UserFinanceController::class, 'transferForm'])->name('chuyen-diem');
    Route::post('chuyen-diem', [UserFinanceController::class, 'processTransfer'])->name('chuyen-diem.store')->middleware('throttle:pin');
    Route::get('ajax/lookup-user', [UserFinanceController::class, 'lookupUser'])->name('ajax.lookup-user')->middleware('throttle:30,1');
});

Route::middleware(['web', 'auth', 'admin', 'permission:setting.edit'])->get('update_tien', [YeuCauRutTienController::class, 'recalculateFees']);
