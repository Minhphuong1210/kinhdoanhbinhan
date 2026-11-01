<?php

use App\Models\User;
use App\Modules\Core\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
Route::middleware(['web'])->get('/api-docs', function () {
    return view('Core::Api.docs');
})->name('api.docs');


Route::middleware(['web', 'auth', 'admin', 'permission:setting.edit'])->get('/number_invite', function () {

    User::chunkById(500, function ($users) {

        foreach ($users as $user) {

            $count = User::where('invite_by', $user->id)
                ->whereNotNull('ngay_tham_gia')
                ->count();

            $user->so_thanh_vien_gt = $count;
            $user->save();
        }

    });

});

/*
 * Tính tiền vat
 * Lấy tiền hoàn nhân với 8% rồi update vào trường này
 * shipping_price mặc định là 54k
 *
 * */

Route::middleware(['web', 'auth', 'admin', 'permission:setting.edit'])->get('/delivery_fee', function () {
    $orders = App\Models\Order::all();
    foreach ($orders as $order) {
        $shipping_price   = ($order->total_price_hoan_tien * 8) / 100;
        $order->price_vat = $shipping_price;
        $order->save();
    }
});

// ============================================================
//  Frontend — trang công khai
// ============================================================
Route::middleware(['web'])->name('frontend.')->group(function () {
    Route::get('/', [\App\Modules\Core\Controllers\Frontend\HomeController::class, 'index'])->name('home');

    // Sản phẩm
    Route::get('/san-pham', [\App\Modules\Core\Controllers\Frontend\ProductController::class, 'index'])->name('product.index');
    Route::get('/san-pham/{slug}', [\App\Modules\Core\Controllers\Frontend\ProductController::class, 'show'])->name('product.show');

    // Tin tức
    Route::get('/tin-tuc', [\App\Modules\Core\Controllers\Frontend\NewsController::class, 'index'])->name('news.index');
    Route::get('/tin-tuc/{slug}', [\App\Modules\Core\Controllers\Frontend\NewsController::class, 'show'])->name('news.show');

    // Trang giới thiệu, liên hệ, hỗ trợ, chính sách
    Route::get('/gioi-thieu', [\App\Modules\Core\Controllers\Frontend\PageController::class, 'about'])->name('about');
    Route::get('/lien-he', [\App\Modules\Core\Controllers\Frontend\PageController::class, 'contact'])->name('contact');
    Route::get('/ho-tro', [\App\Modules\Core\Controllers\Frontend\PageController::class, 'support'])->name('support');
    Route::get('/chinh-sach/{slug}', [\App\Modules\Core\Controllers\Frontend\PageController::class, 'policy'])->name('policy.show');
});

Route::prefix('admin')->name('admin.')->middleware(['web'])->group(function () {
    Route::middleware(['auth', 'admin', 'profile.complete'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard', [DashboardController::class, 'index']);

        // Gói sản phẩm — tất cả user đều xem được
        Route::get('shop', [\App\Modules\Core\Controllers\Admin\ShopController::class, 'index'])->name('shop.index');
        Route::get('shop/buy/{product}', [\App\Modules\Core\Controllers\Admin\ShopController::class, 'buy'])->name('shop.buy');
        Route::post('shop/confirm', [\App\Modules\Core\Controllers\Admin\ShopController::class, 'confirm'])->name('shop.confirm');

        // Tài chính cá nhân
        Route::get('thank-you/{orderId}', [\App\Modules\Core\Controllers\Admin\ShopController::class, 'thankYou'])->name('thank-you');

    });

});
