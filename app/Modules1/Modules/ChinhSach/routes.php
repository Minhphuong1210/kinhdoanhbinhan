<?php

use App\Modules\ChinhSach\Controllers\Admin\ChinhSachController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web'])->group(function () {
    Route::resource('chinh-sachs', ChinhSachController::class)->except(['show']);
});
