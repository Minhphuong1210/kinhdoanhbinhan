<?php

use App\Modules\User\Controllers\Admin\MemberController;
use App\Modules\User\Controllers\Admin\ProfileController;
use App\Modules\User\Controllers\Admin\UserController;
use App\Modules\User\Controllers\Api\MemberApiController;
use App\Modules\User\Controllers\Api\ProfileApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('/profile', [ProfileApiController::class, 'profile']);
    Route::post('/profile/update', [ProfileApiController::class, 'updateProfile']);
    Route::get('/organization-chart', [ProfileApiController::class, 'organizationChart']);
    Route::get('/khach-hang-truc-tiep', [MemberApiController::class, 'khachHangTrucTiep']);
    Route::post('/pin/check', [ProfileApiController::class, 'checkPin'])->middleware('throttle:pin');
    Route::post('/pin/update', [ProfileApiController::class, 'updatePin'])->middleware('throttle:pin');
    Route::post('/account/close', [ProfileApiController::class, 'closeAccount']);
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web'])->group(function () {
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');
    Route::resource('users', UserController::class)->except(['show']);
    Route::get('/users/search', [UserController::class, 'searchUsers'])->name('users.search');
});

Route::prefix('admin')->name('admin.')->middleware(['web'])->group(function () {
    Route::middleware(['auth', 'admin', 'profile.complete'])->group(function () {
        Route::get('my-members', [MemberController::class, 'index'])->name('my-members.index');
        Route::get('my-members/children/{userId}', [MemberController::class, 'children'])->name('my-members.children');
        Route::get('my-referrals', [MemberController::class, 'myReferrals'])->name('my-referrals');
        Route::get('my-tree', [MemberController::class, 'tree'])->name('my-tree');
    });

    // Không qua profile.complete (tránh redirect loop)
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');
    });

    Route::get('profile/info', [ProfileController::class, 'info'])->name('profile.info');
    Route::post('profile/info', [ProfileController::class, 'updateInfo'])->name('profile.info.update');
});
