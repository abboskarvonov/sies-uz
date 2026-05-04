<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\Multimenu;
use App\Models\Page;
use App\Models\SiteSettings;
use App\Models\Submenu;
use App\Models\Symbol;
use App\Observers\MenuObserver;
use App\Observers\PageObserver;
use App\Services\Socialite\HemisEmployeeProvider;
use App\Services\Socialite\HemisStudentProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;

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
        Page::observe(PageObserver::class);
        Menu::observe(MenuObserver::class);
        Submenu::observe(MenuObserver::class);
        Multimenu::observe(MenuObserver::class);

        View::composer('components.main.navbar', function ($view) {
            $menus = Menu::with(['submenus.multimenus'])
                ->where('status', true)
                ->where('position', 'header')
                ->orderBy('order')
                ->get();

            $view->with('menus', $menus);
        });

        View::composer(['components.main.header', 'components.main.footer', 'components.main.nav-logo'], function ($view) {
            static $settings = null;
            $settings ??= SiteSettings::first();
            $view->with('siteSettings', $settings);
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

        // ─── HEMIS Socialite drayverlarini ro'yxatga olish ─────────────
        Socialite::extend('hemis-employee', function ($app) {
            return Socialite::buildProvider(
                HemisEmployeeProvider::class,
                $app['config']['services.hemis_employee']
            );
        });

        Socialite::extend('hemis-student', function ($app) {
            return Socialite::buildProvider(
                HemisStudentProvider::class,
                $app['config']['services.hemis_student']
            );
        });

        if (app()->isProduction() || str_starts_with(config('app.url', ''), 'https')) {
            URL::forceScheme('https');

            // Storage::url() URL facade'ni bypass qilib config'dan o'qiydi.
            // env('APP_URL') http:// bo'lsa ham Storage::url() https:// qaytarishi uchun
            // public disk URL ni ham majburan https:// ga o'tkazamiz.
            $diskUrl = config('filesystems.disks.public.url', '');
            if (str_starts_with($diskUrl, 'http://')) {
                config(['filesystems.disks.public.url' => 'https://' . substr($diskUrl, 7)]);
            }
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
