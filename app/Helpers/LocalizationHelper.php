<?php

use App\Models\Menu;
use App\Models\Multimenu;
use App\Models\Submenu;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;


if (!function_exists('localized_url')) {
    /**
     * Joriy sahifaning boshqa tilga mos URL'ini qaytaradi.
     * Qo'llab-quvvatlanadi:
     *  /{lang}/{menu}
     *  /{lang}/{menu}/{submenu}
     *  /{lang}/{menu}/{submenu}/{multimenu}
     *  /{lang}/{menu}/{submenu}/{multimenu}/{page}
     *  /{lang}/{menu}/{submenu}/{multimenu}/staff/{staff}
     *  /{lang}/{menu}/{submenu}/{multimenu}/{page}/staff/{staff}
     */
    function localized_url(string $locale): string
    {
        $segments = request()->segments();
        if (count($segments) === 0) {
            return LaravelLocalization::getLocalizedURL($locale, url('/'));
        }

        $currentLocale = app()->getLocale();

        $Menu      = \App\Models\Menu::class;
        $Submenu   = \App\Models\Submenu::class;
        $Multimenu = \App\Models\Multimenu::class;
        $Page      = \App\Models\Page::class;
        $Staff     = \App\Models\StaffMember::class;

        $slugColCurrent = 'slug_' . $currentLocale;
        $slugColTarget  = 'slug_' . $locale;

        $getSlug = function ($model, $targetLocale) {
            if (!$model) return null;
            $col = 'slug_' . $targetLocale;
            return $model->{$col} ?? $model->slug_uz ?? null;
        };

        $fallback = fn() => LaravelLocalization::getLocalizedURL($locale, url()->current());

        // lang prefixni olib tashlaymiz:
        $parts = array_values(array_slice($segments, 1));

        try {
            if (count($parts) === 1) {
                [$menuSlug] = $parts;

                $menu = $Menu::where($slugColCurrent, $menuSlug)->first();
                if (!$menu) return $fallback();

                $url = route('pages.show', [
                    'menu' => $getSlug($menu, $locale),
                ], false);

                return LaravelLocalization::getLocalizedURL($locale, $url);
            }

            if (count($parts) === 2) {
                [$menuSlug, $submenuSlug] = $parts;

                $menu = $Menu::where($slugColCurrent, $menuSlug)->first();
                if (!$menu) return $fallback();

                $submenu = $Submenu::where($slugColCurrent, $submenuSlug)
                    ->where('menu_id', $menu->id)->first();
                if (!$submenu) return $fallback();

                $url = route('submenu.index', [
                    'menu'    => $getSlug($menu, $locale),
                    'submenu' => $getSlug($submenu, $locale),
                ], false);

                return LaravelLocalization::getLocalizedURL($locale, $url);
            }

            if (count($parts) === 3) {
                [$menuSlug, $submenuSlug, $multimenuSlug] = $parts;

                $menu = $Menu::where($slugColCurrent, $menuSlug)->first();
                if (!$menu) return $fallback();

                $submenu = $Submenu::where($slugColCurrent, $submenuSlug)
                    ->where('menu_id', $menu->id)->first();
                if (!$submenu) return $fallback();

                $multimenu = $Multimenu::where($slugColCurrent, $multimenuSlug)
                    ->where('menu_id', $menu->id)
                    ->where('submenu_id', $submenu->id)->first();
                if (!$multimenu) return $fallback();

                $url = route('pages.show', [
                    'menu'       => $getSlug($menu, $locale),
                    'submenu'    => $getSlug($submenu, $locale),
                    'multimenu'  => $getSlug($multimenu, $locale),
                ], false);

                return LaravelLocalization::getLocalizedURL($locale, $url);
            }

            if (count($parts) === 4) {
                // /menu/submenu/multimenu/{page}
                [$menuSlug, $submenuSlug, $multimenuSlug, $pageSlug] = $parts;

                $menu = $Menu::where($slugColCurrent, $menuSlug)->first();
                if (!$menu) return $fallback();

                $submenu = $Submenu::where($slugColCurrent, $submenuSlug)
                    ->where('menu_id', $menu->id)->first();
                if (!$submenu) return $fallback();

                $multimenu = $Multimenu::where($slugColCurrent, $multimenuSlug)
                    ->where('menu_id', $menu->id)
                    ->where('submenu_id', $submenu->id)->first();
                if (!$multimenu) return $fallback();

                $page = $Page::query()
                    ->where('menu_id', $menu->id)
                    ->where('submenu_id', $submenu->id)
                    ->where('multimenu_id', $multimenu->id)
                    ->where(function ($q) use ($slugColCurrent, $pageSlug) {
                        $q->where($slugColCurrent, $pageSlug);
                        $id = (int) preg_replace('/^.*-(\d+)$/', '$1', $pageSlug);
                        if ($id > 0) $q->orWhere('id', $id);
                    })
                    ->first();
                if (!$page) return $fallback();

                $targetPageSlug = $page->{$slugColTarget}
                    ?? (($page->{'title_' . $locale} ?? null)
                        ? \Illuminate\Support\Str::slug($page->{'title_' . $locale}) . '-' . $page->id
                        : $page->id);

                $url = route('pages.detail', [
                    'menu'       => $getSlug($menu, $locale),
                    'submenu'    => $getSlug($submenu, $locale),
                    'multimenu'  => $getSlug($multimenu, $locale),
                    'page'       => $targetPageSlug,
                ], false);

                return LaravelLocalization::getLocalizedURL($locale, $url);
            }

            // 5) Staff variantlari:
            if (count($parts) === 5) {
                // /menu/submenu/multimenu/staff/{staff}
                // yoki /menu/submenu/multimenu/{page}/staff/{staff} (praktikada 6 segment bo‘ladi, lekin
                // ayrim rewriteda 5 ko‘rinishi ham mumkin — shuning uchun tekshirib olamiz)
                [$menuSlug, $submenuSlug, $multimenuSlug, $fourth, $fifth] = $parts;

                $menu = $Menu::where($slugColCurrent, $menuSlug)->first();
                if (!$menu) return $fallback();

                $submenu = $Submenu::where($slugColCurrent, $submenuSlug)
                    ->where('menu_id', $menu->id)->first();
                if (!$submenu) return $fallback();

                $multimenu = $Multimenu::where($slugColCurrent, $multimenuSlug)
                    ->where('menu_id', $menu->id)
                    ->where('submenu_id', $submenu->id)->first();
                if (!$multimenu) return $fallback();

                // A) simple: .../staff/{staff}
                if ($fourth === 'staff') {
                    $staffId = $fifth; // id sifatida qoldiramiz
                    $url = route('staff.show.simple', [
                        'menu'       => $getSlug($menu, $locale),
                        'submenu'    => $getSlug($submenu, $locale),
                        'multimenu'  => $getSlug($multimenu, $locale),
                        'staff'      => $staffId,
                    ], false);

                    return LaravelLocalization::getLocalizedURL($locale, $url);
                }

                // B) with page: .../{page}/staff/{staff}  (ideal ko‘rinishi 6 segment)
                // Agar server rewrite 5 segmentga tushirgan bo‘lsa, bu yerda aniqlash qiyin bo‘ladi.
                // Tavsiya: haqiqiy 6 segment holatini ham qo‘llab-quvvatlang (quyida).
                return $fallback();
            }

            // 6) /menu/submenu/multimenu/{page}/staff/{staff}
            if (count($parts) === 6) {
                [$menuSlug, $submenuSlug, $multimenuSlug, $pageSlug, $staffLiteral, $staffId] = $parts;
                if ($staffLiteral !== 'staff') return $fallback();

                $menu = $Menu::where($slugColCurrent, $menuSlug)->first();
                if (!$menu) return $fallback();

                $submenu = $Submenu::where($slugColCurrent, $submenuSlug)
                    ->where('menu_id', $menu->id)->first();
                if (!$submenu) return $fallback();

                $multimenu = $Multimenu::where($slugColCurrent, $multimenuSlug)
                    ->where('menu_id', $menu->id)
                    ->where('submenu_id', $submenu->id)->first();
                if (!$multimenu) return $fallback();

                $page = $Page::query()
                    ->where('menu_id', $menu->id)
                    ->where('submenu_id', $submenu->id)
                    ->where('multimenu_id', $multimenu->id)
                    ->where(function ($q) use ($slugColCurrent, $pageSlug) {
                        $q->where($slugColCurrent, $pageSlug);
                        $id = (int) preg_replace('/^.*-(\d+)$/', '$1', $pageSlug);
                        if ($id > 0) $q->orWhere('id', $id);
                    })
                    ->first();
                if (!$page) return $fallback();

                $targetPageSlug = $page->{$slugColTarget}
                    ?? (($page->{'title_' . $locale} ?? null)
                        ? Str::slug($page->{'title_' . $locale}) . '-' . $page->id
                        : $page->id);

                $url = route('staff.show.withPage', [
                    'menu'       => $getSlug($menu, $locale),
                    'submenu'    => $getSlug($submenu, $locale),
                    'multimenu'  => $getSlug($multimenu, $locale),
                    'page'       => $targetPageSlug,
                    'staff'      => $staffId, // id o‘zgarmaydi
                ], false);

                return LaravelLocalization::getLocalizedURL($locale, $url);
            }

            return $fallback();
        } catch (\Throwable $e) {
            return $fallback();
        }
    }
}

if (!function_exists('localized_page_route')) {
    /**
     * Universal localized URL builder:
     *
     * - menu                           -> pages.show
     * - menu, submenu                  -> submenu.index
     * - menu, submenu, multimenu       -> pages.show
     * - menu, submenu, multimenu, page -> pages.detail
     *
     * @param  \App\Models\Menu            $menu
     * @param  \App\Models\Submenu|null    $submenu
     * @param  \App\Models\Multimenu|null  $multimenu
     * @param  \App\Models\Page|null       $page
     * @param  string|null                 $locale
     * @return string
     */
    function localized_page_route($menu, $submenu = null, $multimenu = null, $page = null, ?string $locale = null): string
    {
        $locale  = $locale ?? app()->getLocale();
        $slugCol = 'slug_' . $locale;

        // Sluglarni xavfsiz olish (fallback: slug_uz -> title -> title-id)
        $menuSlug = $menu->{$slugCol} ?? $menu->slug_uz ?? null;

        $submenuSlug = null;
        if ($submenu) {
            $submenuSlug = $submenu->{$slugCol} ?? $submenu->slug_uz ?? null;
        }

        $multimenuSlug = null;
        if ($multimenu) {
            $multimenuSlug = $multimenu->{$slugCol} ?? $multimenu->slug_uz ?? null;
        }

        $pageSlug = null;
        if ($page) {
            $pageSlug = $page->{$slugCol}
                ?? ($page->{'title_' . $locale} ? Str::slug($page->{'title_' . $locale}) . '-' . $page->id : null);
        }

        // Paramlarni yig'amiz
        $params = ['menu' => $menuSlug];

        // 4 segment: detail
        if ($submenu && $multimenu && $page) {
            $params['submenu']   = $submenuSlug;
            $params['multimenu'] = $multimenuSlug;
            $params['page']      = $pageSlug;

            $url = route('pages.detail', $params, false);
            return LaravelLocalization::getLocalizedURL($locale, $url);
        }

        // 3 segment: list/single (multimenu darajasi)
        if ($submenu && $multimenu) {
            $params['submenu']   = $submenuSlug;
            $params['multimenu'] = $multimenuSlug;

            $url = route('pages.show', $params, false);
            return LaravelLocalization::getLocalizedURL($locale, $url);
        }

        // 2 segment: submenu index
        if ($submenu) {
            $params['submenu'] = $submenuSlug;

            $url = route('submenu.index', $params, false);
            return LaravelLocalization::getLocalizedURL($locale, $url);
        }

        // 1 segment: faqat menu (senda shu route bo'lsa)
        $url = route('pages.show', $params, false);
        return LaravelLocalization::getLocalizedURL($locale, $url);
    }
}

if (!function_exists('localized_staff_url')) {
    /**
     * Xodim profiliga to'g'ri URL qaytaradi (5 yoki 6 segment).
     *
     * @param  \App\Models\Menu              $menu
     * @param  \App\Models\Submenu           $submenu
     * @param  \App\Models\Multimenu         $multimenu
     * @param  \App\Models\StaffMember|int   $staff     // model yoki ID
     * @param  \App\Models\Page|null         $page      // blog/faculty/department uchun kerak
     * @param  string|null                   $locale
     * @return string
     */
    function localized_staff_url($menu, $submenu, $multimenu, $staff, $page = null, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $slugCol = 'slug_' . $locale;

        // Modeldan tilga mos slug olish (fallback: slug_uz)
        $getSlug = function ($model) use ($slugCol) {
            if (!$model) return null;
            return $model->{$slugCol} ?? $model->slug_uz ?? null;
        };

        // Staff ID (model bo‘lsa id, bo‘lmasa integerga cast)
        $staffId = is_object($staff) ? $staff->id : (int) $staff;

        if ($page) {
            // 6 segment: /menu/submenu/multimenu/{page}/staff/{staff}
            $pageSlug = $page->{$slugCol}
                ?? (($page->{'title_' . $locale} ?? null)
                    ? Str::slug($page->{'title_' . $locale}) . '-' . $page->id
                    : (string) $page->id);

            $url = route('staff.show.withPage', [
                'menu'       => $getSlug($menu),
                'submenu'    => $getSlug($submenu),
                'multimenu'  => $getSlug($multimenu),
                'page'       => $pageSlug,
                'staff'      => $staffId,
            ], false);

            return LaravelLocalization::getLocalizedURL($locale, $url);
        }

        // 5 segment: /menu/submenu/multimenu/staff/{staff}
        $url = route('staff.show.simple', [
            'menu'       => $getSlug($menu),
            'submenu'    => $getSlug($submenu),
            'multimenu'  => $getSlug($multimenu),
            'staff'      => $staffId,
        ], false);

        return LaravelLocalization::getLocalizedURL($locale, $url);
    }
}

if (!function_exists('localized_tag_url')) {
    function localized_tag_url(\App\Models\Tag $tag, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $slug = $tag->slug;

        $url = route('tags.show', ['slug' => $slug], false);
        return \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($locale, $url);
    }
}

if (! function_exists('localized_field')) {
    /**
     * Modeldan tilga mos maydonni qaytaradi.
     * Agar mavjud bo‘lmasa → uz tilidagi qiymatni qaytaradi.
     *
     * @param  object      $model   - Eloquent model (Page, StaffMember va h.k.)
     * @param  string      $base    - "title", "name" yoki "content"
     * @param  string|null $locale  - kerakli til (agar null bo‘lsa app()->getLocale())
     * @return string|null
     */
    function localized_field(object $model, string $base, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        $field = $base . '_' . $locale;
        $fallback = $base . '_uz';

        if (!empty($model->{$field})) {
            return $model->{$field};
        }

        return $model->{$fallback} ?? null;
    }
}

if (! function_exists('lc_title')) {
    function lc_title(object $model, ?string $locale = null): ?string
    {
        return localized_field($model, 'title', $locale);
    }
}

if (! function_exists('lc_name')) {
    function lc_name(object $model, ?string $locale = null): ?string
    {
        return localized_field($model, 'name', $locale);
    }
}

if (! function_exists('lc_content')) {
    function lc_content(object $model, ?string $locale = null): ?string
    {
        return localized_field($model, 'content', $locale);
    }
}

if (! function_exists('lc_position')) {
    function lc_position(object $model, ?string $locale = null): ?string
    {
        return localized_field($model, 'position', $locale);
    }
}