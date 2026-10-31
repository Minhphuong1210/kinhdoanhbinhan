<?php

use App\Modules\Setting\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web'])->group(function () {
//    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
//    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
//    Route::get('settings/create', [SettingController::class, 'create'])->name('settings.create');
//    Route::post('settings/store', [SettingController::class, 'store'])->name('settings.store');
//    Route::delete('settings/{setting}', [SettingController::class, 'destroy'])->name('settings.destroy');

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index')->middleware('permission:setting.view');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update')->middleware('permission:setting.edit');
    Route::get('settings/create', [SettingController::class, 'create'])->name('settings.create')->middleware('permission:setting.edit');
    Route::post('settings/store', [SettingController::class, 'store'])->name('settings.store')->middleware('permission:setting.edit');
    Route::delete('settings/{setting}', [SettingController::class, 'destroy'])->name('settings.destroy')->middleware('permission:setting.edit');

});
