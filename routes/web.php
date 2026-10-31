<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Route '/' được định nghĩa trong module Core (Frontend) để hiển thị
| trang chủ với Bootstrap layout. Xem: app/Modules/Core/routes.php
|
*/




Route::get('/view1', function () {
 return view('view1');

});

Route::get('/view2', function () {
 return view('view2');

});

Route::get('/view3', function () {
 return view('view3');

});