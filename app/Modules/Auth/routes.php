<?php

use App\Modules\Auth\Controllers\Admin\AuthController;
use App\Modules\Auth\Controllers\Api\AuthApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware('api')->group(function () {
    Route::post('/login', [AuthApiController::class, 'login'])->middleware('throttle:login');
    Route::post('/register', [AuthApiController::class, 'register'])->middleware('throttle:sensitive');
    Route::post('/forgot-password', [AuthApiController::class, 'forgotPassword'])->middleware('throttle:sensitive');
    Route::post('/forgot-pin', [AuthApiController::class, 'forgotPin'])->middleware('throttle:sensitive');

    Route::post('/logout', [AuthApiController::class, 'logout'])->middleware('auth:sanctum');
});

Route::prefix('admin')->name('admin.')->middleware(['web'])->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit')->middleware('throttle:login');
    Route::get('forgot-pin', [AuthController::class, 'showForgotPin'])->name('forgot-pin');
    Route::post('forgot-pin', [AuthController::class, 'sendForgotPin'])->name('forgot-pin.submit')->middleware('throttle:sensitive');
    Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('forgot-password', [AuthController::class, 'sendForgotPassword'])->name('forgot-password.submit')->middleware('throttle:sensitive');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register'])->name('register.submit')->middleware('throttle:sensitive');

    Route::get('/users/{user}/login', [AuthController::class, 'loginAsUser'])
        ->name('dangnhaptaikhoancuaho')
        ->middleware(['auth', 'admin', 'permission:user.edit']);
});
