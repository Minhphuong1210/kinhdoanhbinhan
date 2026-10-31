<?php

use App\Modules\Notifycation\Controllers\Admin\BroadcastNotificationController;
use App\Modules\Notifycation\Controllers\Api\NotificationApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('/notifications', [NotificationApiController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationApiController::class, 'unreadCount']);
    Route::get('/notifications/{notification}', [NotificationApiController::class, 'show'])->whereNumber('notification');
    Route::post('/notifications/{notification}/read', [NotificationApiController::class, 'markAsRead']);
});

// Admin gửi thông báo cho tất cả mọi người (app công ty / app HTX / cả 2)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'web', 'permission:content.manage'])->group(function () {
    Route::post('thong-bao/upload-image', [BroadcastNotificationController::class, 'uploadImage'])->name('thong-bao.upload-image');
    Route::resource('thong-bao', BroadcastNotificationController::class)->except(['show']);
});
