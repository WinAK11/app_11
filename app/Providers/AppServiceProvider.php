<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;


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
        //
        View::composer('*', function ($view) {
        $categories = Category::withCount('products')
                            ->orderByDesc('products_count')
                            ->take(5)
                            ->get();
        $view->with('footerCategories', $categories);

    });
    }
}
