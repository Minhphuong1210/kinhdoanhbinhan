<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Token của tài khoản bị khoá không còn dùng được
        Sanctum::authenticateAccessTokensUsing(fn ($token, bool $isValid) => $isValid && (bool) $token->tokenable?->active);

        // Blade directive: @sharedAsset('products/abc.jpg')
        // → asset('shared/products/abc.jpg')
        Blade::directive('sharedAsset', function ($path) {
            return "<?php echo asset('shared/' . ltrim($path, '/')); ?>";
        });

        // Custom pagination view cho admin (không dùng Bootstrap)
        if (request()->is('admin/*') || request()->is('admin')) {
            Paginator::defaultView('vendor.pagination.admin');
            Paginator::defaultSimpleView('vendor.pagination.admin');
        }
    }
}

