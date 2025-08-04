<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\Facades\View;
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
        View::composer('components.main.navbar', function ($view) {
            $menus = Menu::with(['submenus.multimenus'])
                ->where('status', true)
                ->orderBy('order')
                ->get();

            $view->with('menus', $menus);
        });
    }
}
