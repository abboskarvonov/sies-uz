<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\MenuResource;
use App\Http\Resources\Api\V1\SubmenuResource;
use App\Http\Traits\Api\ApiResponses;
use App\Models\Menu;
use App\Models\Submenu;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $locale = app()->getLocale();
        $ttl = config('site.cache.ttl.menu', 3600);
        $cacheKey = "api:menu_tree_{$locale}";

        $menus = config('site.cache.enabled')
            ? Cache::remember($cacheKey, $ttl, fn () => $this->getMenuTree())
            : $this->getMenuTree();

        return $this->successResponse(MenuResource::collection($menus));
    }

    public function show(string $slug)
    {
        $locale = app()->getLocale();

        $menu = Menu::with(['submenus' => fn ($q) => $q->where('status', true)->orderBy('order')])
            ->where('status', true)
            ->where(function ($q) use ($locale, $slug) {
                $q->where("slug_{$locale}", $slug)->orWhere('slug_uz', $slug);
            })
            ->first();

        if (!$menu) {
            return $this->notFoundResponse('Menu not found');
        }

        return $this->successResponse(new MenuResource($menu));
    }

    public function submenu(string $menuSlug, string $submenuSlug)
    {
        $locale = app()->getLocale();

        $menu = Menu::where('status', true)
            ->where(function ($q) use ($locale, $menuSlug) {
                $q->where("slug_{$locale}", $menuSlug)->orWhere('slug_uz', $menuSlug);
            })
            ->first();

        if (!$menu) {
            return $this->notFoundResponse('Menu not found');
        }

        $submenu = Submenu::with(['multimenus' => fn ($q) => $q->where('status', true)->orderBy('order')])
            ->where('menu_id', $menu->id)
            ->where('status', true)
            ->where(function ($q) use ($locale, $submenuSlug) {
                $q->where("slug_{$locale}", $submenuSlug)->orWhere('slug_uz', $submenuSlug);
            })
            ->first();

        if (!$submenu) {
            return $this->notFoundResponse('Submenu not found');
        }

        return $this->successResponse(new SubmenuResource($submenu));
    }

    private function getMenuTree()
    {
        return Menu::with(['submenus' => function ($q) {
                $q->where('status', true)
                    ->orderBy('order')
                    ->with(['multimenus' => fn ($mq) => $mq->where('status', true)->orderBy('order')]);
            }])
            ->where('status', true)
            ->orderBy('order')
            ->get();
    }
}
