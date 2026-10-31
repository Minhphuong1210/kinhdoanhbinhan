<?php

namespace App\Modules\Core\Providers;

use App\Models\CategoryPost;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class FrontendComposerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Share danh mục tin tức (category_post) cho toàn bộ frontend layout
        // → dùng trong nav dropdown "Tin tức"
        View::composer('Core::Frontend.layouts.app', function ($view) {
            $view->with('navPostCategories', CategoryPost::where('active', 1)->get());
        });
    }
}
