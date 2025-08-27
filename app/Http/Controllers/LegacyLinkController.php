<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Page;
use App\Models\Submenu;
use App\Models\Multimenu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LegacyLinkController extends Controller
{
    public function show(Request $request, string $legacy)
    {
        // 1) Eski format: 1675056495_ilm_fan_iftixori__... → raqamlar va 1 ta pastki chiziqni olib tashlaymiz
        $clean = preg_replace('/^\d+_/', '', $legacy) ?? $legacy;

        // 2) Ba’zan eski sluglarda "_" ishlatilgan bo‘ladi, slug’ga o‘tkazamiz
        $candidate = Str::slug(str_replace('_', ' ', $clean));

        // 3) Page’ni slug bo‘yicha topish (uz/ru/en variantlarni tekshiramiz)
        $page = Page::query()
            ->where('slug_uz', $candidate)
            ->orWhere('slug_ru', $candidate)
            ->orWhere('slug_en', $candidate)
            ->first();

        // Agar topilmasa – original `$clean` bilan yana bir bor urunib ko‘ramiz
        if (!$page) {
            $page = Page::query()
                ->where('slug_uz', $clean)
                ->orWhere('slug_ru', $clean)
                ->orWhere('slug_en', $clean)
                ->first();
        }

        // Hali ham topilmasa – bosh sahifaga yoki 404
        if (!$page) {
            return redirect()->route('home'); // yoki: abort(404);
        }

        // 4) Menu/Submenu/Multimenu modelini oldik
        $menu      = Menu::find($page->menu_id);
        $submenu   = Submenu::find($page->submenu_id);
        $multimenu = Multimenu::find($page->multimenu_id);

        if (!$menu || !$submenu || !$multimenu) {
            // Kontekst yetarli bo‘lmasa – xavfsiz fallback
            return redirect()->route('home');
        }

        // 5) Yangi kanonik URL’ga 301 redirect
        // Sizda ilgari yozib bergan `localized_page_route($menu, $submenu, $multimenu, $page, $locale=null)` helper bor:
        $url = localized_page_route($menu, $submenu, $multimenu, $page);

        return redirect()->to($url, 301);
    }
}