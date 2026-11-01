<?php

use App\Modules\Home\Controllers\Frontend\HomeController;
use App\Modules\Home\Controllers\Frontend\PageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->name('frontend.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Trang giới thiệu, liên hệ, hỗ trợ, chính sách
    Route::get('/gioi-thieu', [PageController::class, 'about'])->name('about');
    Route::get('/lien-he', [PageController::class, 'contact'])->name('contact');
    Route::get('/ho-tro', [PageController::class, 'support'])->name('support');
    Route::get('/chinh-sach/{slug}', [PageController::class, 'policy'])->name('policy.show');
});
