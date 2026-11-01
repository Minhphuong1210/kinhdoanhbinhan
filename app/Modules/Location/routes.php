<?php

use App\Modules\Location\Controllers\Admin\LocationAjaxController;
use App\Modules\Location\Controllers\Api\LookupApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware('api')->group(function () {
    Route::get('/register-options', [LookupApiController::class, 'registerOptions']);
    Route::get('/provinces', [LookupApiController::class, 'provinces']);
    Route::get('/districts', [LookupApiController::class, 'districts']);
    Route::get('/wards', [LookupApiController::class, 'wards']);
    Route::get('/bank-areas', [LookupApiController::class, 'bankAreas']);
    Route::get('/banks', [LookupApiController::class, 'banks']);
    Route::get('/bank-branches', [LookupApiController::class, 'bankBranches']);
});

Route::prefix('admin')->name('admin.')->middleware(['web'])->group(function () {
    Route::get('ajax/districts/{province_id}', [LocationAjaxController::class, 'districts'])->name('ajax.districts');
    Route::get('ajax/wards/{district_id}', [LocationAjaxController::class, 'wards'])->name('ajax.wards');
    Route::get('ajax/pancake-districts/{province_id}', [LocationAjaxController::class, 'pancakeDistricts'])->name('ajax.pancake_districts');
    Route::get('ajax/pancake-wards/{district_id}', [LocationAjaxController::class, 'pancakeWards'])->name('ajax.pancake_wards');
    Route::get('ajax/bank-cities', [LocationAjaxController::class, 'bankCities'])->name('ajax.bank-cities');
    Route::get('ajax/branches/{city_id}', [LocationAjaxController::class, 'branches'])->name('ajax.branches');
    // Giữ lại route cũ để tương thích
    Route::get('ajax/banks-by-city/{city_id}', [LocationAjaxController::class, 'branches'])->name('ajax.banks-by-city');
});
