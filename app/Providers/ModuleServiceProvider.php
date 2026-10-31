<?php

namespace App\Providers;

use App\Models\CategoryPost;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $modulesPath = app_path('Modules');

        if (!is_dir($modulesPath)) {
            return;
        }

        foreach (scandir($modulesPath) as $module) {
            if (in_array($module, ['.','..'])) continue;
            $moduleDir = $modulesPath . DIRECTORY_SEPARATOR . $module;
            if (!is_dir($moduleDir)) continue;

            $this->loadModule($moduleDir, $module);
        }

        // Share danh mục tin tức (category_post) cho toàn bộ frontend layout
        // Dùng try/catch để tránh lỗi khi bảng chưa tồn tại (migrate)
        try {
            View::composer('Core::Frontend.layouts.app', function ($view) {
                $view->with('navPostCategories', CategoryPost::where('active', 1)->get());
            });

            // Thông tin doanh nghiệp, menu, chính sách cho layout + mọi trang frontend
            View::composer([
                'Core::Frontend.layouts.app',
                'Core::Frontend.home.index',
                'Core::Frontend.products.show',
                'Core::Frontend.pages.*',
            ], \App\Modules\Core\ViewComposers\FrontendLayoutComposer::class);
        } catch (\Throwable $e) {
            // Bỏ qua lỗi DB khi chạy migrate/artisan
        }
    }

    private function loadModule(string $moduleDir, string $moduleName): void
    {
        // Load routes/admin.php (cấu trúc mới)
        $routeAdmin = $moduleDir . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'admin.php';
        if (file_exists($routeAdmin)) {
            $this->loadRoutesFrom($routeAdmin);
        }

        // Backward compat: routes.php trực tiếp
        $routeFile = $moduleDir . DIRECTORY_SEPARATOR . 'routes.php';
        if (file_exists($routeFile)) {
            $this->loadRoutesFrom($routeFile);
        }

        // Register view namespace từ Views/ root → Module::Admin.view
        $viewsDir = $moduleDir . DIRECTORY_SEPARATOR . 'Views';
        if (is_dir($viewsDir)) {
            $this->loadViewsFrom($viewsDir, $moduleName);
        }
    }
}
