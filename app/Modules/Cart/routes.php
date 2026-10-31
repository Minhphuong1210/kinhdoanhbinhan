<?php

use App\Modules\Cart\Controllers\Api\CartApiController;
use Illuminate\Support\Facades\Route;

// Giỏ hàng — chỉ dành cho sản phẩm mua tự chọn
Route::prefix('api/cart')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('/', [CartApiController::class, 'index']);
    Route::get('/count', [CartApiController::class, 'count']);
    Route::delete('/', [CartApiController::class, 'clear']);

    Route::post('/items', [CartApiController::class, 'store']);
    Route::put('/items/{item}', [CartApiController::class, 'update'])->whereNumber('item');
    Route::delete('/items/{item}', [CartApiController::class, 'destroy'])->whereNumber('item');
});
