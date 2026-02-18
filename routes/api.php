<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Public\HomepageController;
use App\Http\Controllers\Api\V1\Public\MenuController;
use App\Http\Controllers\Api\V1\Public\PageController;
use App\Http\Controllers\Api\V1\Public\SearchController;
use App\Http\Controllers\Api\V1\Public\SiteStatController;
use App\Http\Controllers\Api\V1\Public\StaffController;
use App\Http\Controllers\Api\V1\Public\SymbolController;
use App\Http\Controllers\Api\V1\Public\TagController;
use App\Http\Controllers\Api\V1\Admin\PageController as AdminPageController;
use App\Http\Controllers\Api\V1\Admin\StaffMemberController as AdminStaffController;
use App\Http\Controllers\Api\V1\Admin\ActivityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->middleware(['force.json', 'set.locale'])->group(function () {

    // ─── Public API (autentifikatsiyasiz) ────────────────────
    Route::middleware('throttle:api-public')->group(function () {

        // Homepage
        Route::get('homepage', [HomepageController::class, 'index']);

        // Menus
        Route::get('menus', [MenuController::class, 'index']);
        Route::get('menus/{slug}', [MenuController::class, 'show']);
        Route::get('menus/{menu}/{submenu}', [MenuController::class, 'submenu']);

        // Pages
        Route::get('pages', [PageController::class, 'index']);
        Route::get('pages/{id}', [PageController::class, 'show'])->where('id', '[0-9]+');
        Route::get('pages/by-path/{menu}/{sub}/{multi}', [PageController::class, 'byPath']);
        Route::get('pages/by-path/{menu}/{sub}/{multi}/{page}', [PageController::class, 'detailByPath']);

        // Search
        Route::get('search', [SearchController::class, 'index']);

        // Tags
        Route::get('tags', [TagController::class, 'index']);
        Route::get('tags/{slug}', [TagController::class, 'show']);

        // Staff (public)
        Route::get('staff/{id}', [StaffController::class, 'show'])->where('id', '[0-9]+');
        Route::get('pages/{id}/staff', [StaffController::class, 'byPage'])->where('id', '[0-9]+');

        // Symbols
        Route::get('symbols', [SymbolController::class, 'index']);
        Route::get('symbols/{slug}', [SymbolController::class, 'show']);

        // Stats
        Route::get('stats', [SiteStatController::class, 'index']);
    });

    // ─── Auth API ────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:api-login');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('profile', [AuthController::class, 'profile']);
            Route::put('profile', [AuthController::class, 'updateProfile']);
        });
    });

    // ─── Admin API (auth:sanctum + policy authorization) ─────
    Route::prefix('admin')->middleware(['auth:sanctum', 'throttle:api-auth'])->group(function () {

        // Pages CRUD
        Route::apiResource('pages', AdminPageController::class)->names('api.admin.pages');

        // Staff CRUD
        Route::apiResource('staff', AdminStaffController::class)->names('api.admin.staff');

        // Activities (read-only)
        Route::get('activities', [ActivityController::class, 'index']);
    });
});
