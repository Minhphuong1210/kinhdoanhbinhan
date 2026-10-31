<?php

use App\Modules\Widget\Controllers\Admin\WidgetController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web'])->group(function () {
    Route::resource('widgets', WidgetController::class)->except(['show']);
});
