<?php

use App\Http\Controllers\LegacyLinkController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SymbolController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {
        // Home page
        Route::get('/', [PageController::class, 'index'])->name('home');
        Route::get('/search', [SearchController::class, 'index'])->name('search');
        // ✅ Symbol routes uchun alohida prefix
        Route::prefix('symbol')->group(function () {
            Route::get('/{symbol}', [SymbolController::class, 'show'])->name('symbol.show');
        });
    }
);

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Bu yerda boshqa admin panel yoki user kabinet routelaring bo‘lishi mumkin
});

Route::get('pagesView/view/{legacy}', [LegacyLinkController::class, 'show'])
    ->name('legacy.page');

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {
        Route::get('tags/', [PageController::class, 'tagsIndex'])->name('tags');

        Route::get('tags/{slug}', [PageController::class, 'tagsShow'])->name('tags.show');

        // 5 segment: /menu/submenu/multimenu/staff/{staff}
        Route::get('{menu}/{submenu}/{multimenu}/staff/{staff}', [PageController::class, 'staffShowSimple'])
            ->name('staff.show.simple')
            ->where([
                'menu' => '[A-Za-z0-9\-_]+',
                'submenu' => '[A-Za-z0-9\-_]+',
                'multimenu' => '[A-Za-z0-9\-_]+',
                'staff' => '[A-Za-z0-9\-_]+', // slug yoki id
            ]);

        // 6 segment: /menu/submenu/multimenu/page/staff/{staff}
        Route::get('{menu}/{submenu}/{multimenu}/{page}/staff/{staff}', [PageController::class, 'staffShowWithPage'])
            ->name('staff.show.withPage')
            ->where([
                'menu' => '[A-Za-z0-9\-_]+',
                'submenu' => '[A-Za-z0-9\-_]+',
                'multimenu' => '[A-Za-z0-9\-_]+',
                'page' => '.*',      // slug-123 yoki oddiy slug
                'staff' => '[A-Za-z0-9\-_]+', // slug yoki id
            ]);

        // Menu page (1 segment)
        Route::get('{menu}', [PageController::class, 'menuIndex'])
            ->name('menu.index');

        //Submenu index (2 segment) — AVVAL!
        Route::get('{menu}/{submenu}', [PageController::class, 'submenuIndex'])
            ->name('submenu.index')
            ->where([
                'menu'    => '[A-Za-z0-9\-_]+',
                'submenu' => '[A-Za-z0-9\-_]+',
            ]);

        // BLOG DETAIL (4 segment)
        Route::get('{menu}/{submenu}/{multimenu}/{page}', [PageController::class, 'pageDetail'])
            ->name('pages.detail')
            ->where([
                'menu' => '[A-Za-z0-9\-_]+',
                'submenu' => '[A-Za-z0-9\-_]+',
                'multimenu' => '[A-Za-z0-9\-_]+',
                'page' => '[A-Za-z0-9\-_]+',
            ]);

        Route::get('{menu}/{submenu?}/{multimenu?}', [PageController::class, 'pagesShow'])
            ->where([
                // menu = faqat harf/son/-/_ bo‘lsin va "dashboard" so‘zini chiqarib tashlaymiz
                'menu' => '^(?!dashboard$)[A-Za-z0-9\-_]+',
                'submenu' => '[A-Za-z0-9\-_]+',
                'multimenu' => '[A-Za-z0-9\-_]+',
            ])
            ->name('pages.show');
    }
);
