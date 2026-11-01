<?php

use Illuminate\Support\Facades\Route;

// Core chỉ giữ view dùng chung, chức năng nằm trong từng module
Route::middleware(['web'])->get('/api-docs', fn () => view('Core::Api.docs'))->name('api.docs');
