<?php

use App\Modules\Media\Controllers\Api\MediaApiController;
use Illuminate\Support\Facades\Route;

Route::prefix(config('media.routes.prefix'))
    ->middleware(config('media.routes.middleware'))
    ->group(function () {
        Route::post('/', [MediaApiController::class, 'store'])->middleware(config('media.routes.throttle'));
        Route::get('/url', [MediaApiController::class, 'url']);
    });
