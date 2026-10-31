<?php

use App\Models\PancakeCommunes;
use App\Models\PancakeDistricts;
use App\Models\User;
use App\Modules\Core\Controllers\Admin\AuthController;
use App\Modules\Core\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Modules\Core\Controllers\Api\AuthApiController;
use App\Modules\Core\Controllers\Api\LookupApiController;
Route::middleware(['web'])->get('/api-docs', function () {
    return view('Core::Api.docs');
})->name('api.docs');

Route::prefix('api')->middleware('api')->group(function () {
    Route::post('/login', [AuthApiController::class, 'login']);
    Route::post('/register', [AuthApiController::class, 'register']);
    Route::post('/forgot-password', [AuthApiController::class, 'forgotPassword']);
    Route::post('/forgot-pin', [AuthApiController::class, 'forgotPin']);
    Route::get('/register-options', [LookupApiController::class, 'registerOptions']);
    Route::get('/provinces', [LookupApiController::class, 'provinces']);
    Route::get('/districts', [LookupApiController::class, 'districts']);
    Route::get('/wards', [LookupApiController::class, 'wards']);
    Route::get('/bank-areas', [LookupApiController::class, 'bankAreas']);
    Route::get('/banks', [LookupApiController::class, 'banks']);
    Route::get('/bank-branches', [LookupApiController::class, 'bankBranches']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthApiController::class, 'logout']);
        Route::get('/profile', [AuthApiController::class, 'profile']);
        Route::post('/profile/update', [AuthApiController::class, 'updateProfile']);
        Route::get('/organization-chart', [AuthApiController::class, 'organizationChart']);
        Route::get('/khach-hang-truc-tiep', [\App\Modules\Core\Controllers\Api\MemberApiController::class, 'khachHangTrucTiep']);
        Route::post('/pin/check', [AuthApiController::class, 'checkPin']);
        Route::post('/pin/update', [AuthApiController::class, 'updatePin']);
        Route::post('/account/close', [AuthApiController::class, 'closeAccount']);
    });
});

Route::get('/number_invite', function () {

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

Route::get('/delivery_fee', function () {
    $orders = App\Models\Order::all();
    foreach ($orders as $order) {
        $shipping_price   = ($order->total_price_hoan_tien * 8) / 100;
        $order->price_vat = $shipping_price;
        $order->save();
    }
});
Route::get('update_tien', function () {
    $yeuCauRutTiens = App\Models\YeuCauRutTien::all();

    foreach ($yeuCauRutTiens as $yeuCauRutTien) {
        $tien_10_phan_tram  = ($yeuCauRutTien->so_tien * 10) / 100;
        $tien_1_5_phan_tram = ($yeuCauRutTien->so_tien * 1.5) / 100;
       
        $yeuCauRutTien->{'10_phan_tram_so_tien'}  = $tien_10_phan_tram;
        $yeuCauRutTien->{'1_5_phan_tram_so_tien'} = $tien_1_5_phan_tram;
        $yeuCauRutTien->save();
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
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('forgot-pin', [AuthController::class, 'showForgotPin'])->name('forgot-pin');
    Route::post('forgot-pin', [AuthController::class, 'sendForgotPin'])->name('forgot-pin.submit');
    Route::get('forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('forgot-password', [AuthController::class, 'sendForgotPassword'])->name('forgot-password.submit');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register'])->name('register.submit');

    // AJAX địa chỉ hành chính — không yêu cầu auth, dùng được cả ở trang đăng ký
    Route::get('ajax/districts/{province_id}', function ($province_id) {
        $formattedProvinceId = str_pad($province_id, 2, '0', STR_PAD_LEFT);
        return response()->json(
            \App\Models\Districts::where('province_id', $formattedProvinceId)
                ->orderBy('name')
                ->get(['id', 'name', 'prefix'])
        );
    })->name('ajax.districts');

    Route::get('ajax/wards/{district_id}', function ($district_id) {
        // Nếu district_id là số và cần format về dạng 001, 002...
        $formattedDistrictId = str_pad($district_id, 3, '0', STR_PAD_LEFT);

        return response()->json(
            \App\Models\Wards::where('district_id', $formattedDistrictId)
                ->orderBy('name')
                ->get(['id', 'name', 'prefix'])
        );
    })->name('ajax.wards');

    Route::get('ajax/pancake-districts/{province_id}', function ($province_id) {
        return response()->json(
            PancakeDistricts::where('province_id', $province_id)
                ->orderBy('name')
                ->get(['id_pancake as id', 'name'])
        );
    })->name('ajax.pancake_districts');

    Route::get('ajax/pancake-wards/{district_id}', function ($district_id) {
        return response()->json(
            PancakeCommunes::where('district_id', $district_id)
                ->orderBy('name')
                ->get(['id_pancake as id', 'name'])
        );
    })->name('ajax.pancake_wards');

    Route::get('ajax/bank-cities', function () {
        return response()->json(
            \App\Models\ThanhPhoBanks::orderBy('name')->get(['id', 'name'])
        );
    })->name('ajax.bank-cities');

    // Lấy chi nhánh theo thành phố + ngân hàng lớn
    Route::get('ajax/branches/{city_id}', function ($city_id, ) {
        return response()->json(
            \App\Models\NganHangBank::where('thanh_pho_id', $city_id)

                ->orderBy('name')
                ->get(['id', 'name'])
        );
    })->name('ajax.branches');

    // Giữ lại route cũ để tương thích (lấy tất cả chi nhánh theo thành phố)
    Route::get('ajax/banks-by-city/{city_id}', function ($city_id) {
        return response()->json(
            \App\Models\NganHangBank::where('thanh_pho_id', $city_id)
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    })->name('ajax.banks-by-city');

    Route::middleware(['auth', 'admin', 'profile.complete'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard', [DashboardController::class, 'index']);

        // Gói sản phẩm — tất cả user đều xem được
        Route::get('shop', [\App\Modules\Core\Controllers\Admin\ShopController::class, 'index'])->name('shop.index');
        Route::get('shop/buy/{product}', [\App\Modules\Core\Controllers\Admin\ShopController::class, 'buy'])->name('shop.buy');
        Route::post('shop/confirm', [\App\Modules\Core\Controllers\Admin\ShopController::class, 'confirm'])->name('shop.confirm');
        Route::get('my-members', [\App\Modules\Core\Controllers\Admin\MemberController::class, 'index'])->name('my-members.index');
        Route::get('my-members/children/{userId}', [\App\Modules\Core\Controllers\Admin\MemberController::class, 'children'])->name('my-members.children');

        // Tài chính cá nhân
        Route::get('my-transactions', [\App\Modules\Core\Controllers\Admin\UserFinanceController::class, 'transactions'])->name('my-transactions');
        Route::get('my-withdrawals', [\App\Modules\Core\Controllers\Admin\UserFinanceController::class, 'withdrawals'])->name('my-withdrawals');
        Route::post('my-withdrawals', [\App\Modules\Core\Controllers\Admin\UserFinanceController::class, 'requestWithdrawal'])->name('my-withdrawals.store');
        Route::get('my-referrals', [\App\Modules\Core\Controllers\Admin\MemberController::class, 'myReferrals'])->name('my-referrals');
        Route::get('my-income', [\App\Modules\Core\Controllers\Admin\IncomeController::class, 'index'])->name('my-income');
        Route::get('my-tree', [\App\Modules\Core\Controllers\Admin\TreeController::class, 'index'])->name('my-tree');
        Route::get('thank-you/{orderId}', [\App\Modules\Core\Controllers\Admin\ShopController::class, 'thankYou'])->name('thank-you');
        Route::get('chuyen-diem', [\App\Modules\Core\Controllers\Admin\UserFinanceController::class, 'transferForm'])->name('chuyen-diem');
        Route::post('chuyen-diem', [\App\Modules\Core\Controllers\Admin\UserFinanceController::class, 'processTransfer'])->name('chuyen-diem.store');
        Route::get('ajax/lookup-user', [\App\Modules\Core\Controllers\Admin\UserFinanceController::class, 'lookupUser'])->name('ajax.lookup-user');

    });

    // Profile + AJAX hành chính — auth + admin, KHÔNG qua profile.complete (tránh redirect loop)
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('profile/edit', [\App\Modules\Core\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('profile/update', [\App\Modules\Core\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

    });
    Route::get('profile/info', [\App\Modules\Core\Controllers\Admin\ProfileController::class, 'info'])->name('profile.info');
    Route::post('profile/info', [\App\Modules\Core\Controllers\Admin\ProfileController::class, 'updateInfo'])->name('profile.info.update');
});
