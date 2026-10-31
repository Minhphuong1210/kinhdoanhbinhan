<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

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

