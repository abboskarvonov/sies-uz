<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\Symbol;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('/livewire/update', $handle)
                ->middleware([
                    'web',
                    \Illuminate\Session\Middleware\StartSession::class,
                    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                ]);
        });

        View::composer('components.main.navbar', function ($view) {
            $menus = Menu::with(['submenus.multimenus'])
                ->where('status', true)
                ->where('position', 'header')
                ->orderBy('order')
                ->get();

            $view->with('menus', $menus);
        });

        View::composer('components.main.quick-links', function ($view) {
            $quickLinks = Menu::where('status', true)
                ->where('position', 'quick_links')
                ->orderBy('order')
                ->get();

            $view->with('quickLinks', $quickLinks);
        });

        View::composer('*', function ($view) {
            $symbolSlugs = [
                'flag' => Symbol::where("slug_uz", '!=', null)
                    ->where("title_uz", 'like', '%bayrog%')
                    ->value("slug_uz"),

                'emblem' => Symbol::where("slug_uz", '!=', null)
                    ->where("title_uz", 'like', '%gerb%')
                    ->value("slug_uz"),

                'anthem' => Symbol::where("slug_uz", '!=', null)
                    ->where("title_uz", 'like', '%madhiy%')
                    ->value("slug_uz"),
            ];

            $view->with('symbolSlugs', $symbolSlugs);
        });

        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        // API Rate Limiters
        RateLimiter::for('api-public', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        RateLimiter::for('api-auth', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('api-login', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email', $request->ip()));
        });
    }
}
