<?php

use App\Modules\User\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web'])->group(function () {
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');
    Route::resource('users', UserController::class)->except(['show']);
    Route::get('/users/search', [UserController::class, 'searchUsers'])
        ->name('users.search');

    Route::get('/users/{id}/login', [UserController::class, 'AdminLogin'])->name('dangnhaptaikhoancuaho');

});
