<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('web', function (Request $request) {
            return Limit::perMinute(300)->by($request->user()?->id ?: $request->ip());
        });

        // Đăng nhập: giới hạn theo tài khoản + IP và theo IP để chống dò mật khẩu
        RateLimiter::for('login', function (Request $request) {
            $login = (string) ($request->input('login') ?? $request->input('email'));

            return [
                Limit::perMinute(5)->by(mb_strtolower($login) . '|' . $request->ip()),
                Limit::perMinute(30)->by($request->ip()),
            ];
        });

        // Đăng ký, quên mật khẩu, quên PIN
        RateLimiter::for('sensitive', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Chống dò PIN theo tài khoản
        RateLimiter::for('pin', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));


            foreach (glob(base_path('app/Modules/*/routes.php')) as $routeFile) {
                require $routeFile;
            }

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
