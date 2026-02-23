<?php

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

        $currentLocale  = app()->getLocale();
        $slugColCurrent = 'slug_' . $currentLocale;
        $slugColTarget  = 'slug_' . $locale;

        $Menu      = \App\Models\Menu::class;
        $Submenu   = \App\Models\Submenu::class;
        $Multimenu = \App\Models\Multimenu::class;
        $Page      = \App\Models\Page::class;

        $fallback = fn() => LaravelLocalization::getLocalizedURL($locale, url()->current());

        // lang prefixni olib tashlaymiz:
        $parts = array_values(array_slice($segments, 1));

        // ── Request-level model cache ──────────────────────────────────────
        // localized_url() til almashtirish uchun 2-3 marta chaqiriladi (har bir til uchun).
        // Bir so'rov ichida bir xil slugni qayta DB dan olmaslik uchun static cache.
        static $modelCache = [];

        $resolve = function (string $class, string $slug, array $where = []) use (&$modelCache, $slugColCurrent) {
            $key = $class . '|' . $slugColCurrent . '|' . $slug . '|' . serialize($where);
            return $modelCache[$key] ??= $class::where($slugColCurrent, $slug)->where($where)->first();
        };

        // Slug olish: target locale → slug_uz fallback
        $getSlug = fn($model) => $model ? ($model->{'slug_' . $locale} ?? $model->slug_uz ?? null) : null;

        // Page uchun target slug aniqlash (fallback zanjiri bilan)
        $pageTargetSlug = function ($page) use ($slugColTarget, $locale) {
            $s = $page->{$slugColTarget};
            if ($s !== null && $s !== '') return $s;

            $s = $page->slug_uz;
            if ($s !== null && $s !== '') return $s;

            return $page->{'title_' . $locale}
                ? Str::slug($page->{'title_' . $locale}) . '-' . $page->id
                : (string) $page->id;
        };

        // Page qidirish: slug, slug-id, numeric ID, boshqa til sluglari
        $findPage = function ($menuId, $submenuId, $multimenuId, string $pageSlug) use ($Page, $slugColCurrent) {
            return $Page::query()
                ->where('menu_id', $menuId)
                ->where('submenu_id', $submenuId)
                ->where('multimenu_id', $multimenuId)
                ->where(function ($q) use ($slugColCurrent, $pageSlug) {
                    $q->where($slugColCurrent, $pageSlug);

                    preg_match('/-(\d+)$/', $pageSlug, $m);
                    $id = (int) ($m[1] ?? 0);
                    if ($id > 0) $q->orWhere('id', $id);

                    if (is_numeric($pageSlug)) $q->orWhere('id', (int) $pageSlug);

                    $q->orWhere('slug_uz', $pageSlug)
                        ->orWhere('slug_ru', $pageSlug)
                        ->orWhere('slug_en', $pageSlug);
                })
                ->first();
        };

        try {
            // 1 segment: /menu
            if (count($parts) === 1) {
                $menu = $resolve($Menu, $parts[0]);
                if (!$menu) return $fallback();

                return LaravelLocalization::getLocalizedURL(
                    $locale,
                    route('pages.show', ['menu' => $getSlug($menu)], false)
                );
            }

            // 2 segment: /menu/submenu
            if (count($parts) === 2) {
                [$menuSlug, $submenuSlug] = $parts;
                $menu = $resolve($Menu, $menuSlug);
                if (!$menu) return $fallback();

                $submenu = $resolve($Submenu, $submenuSlug, ['menu_id' => $menu->id]);
                if (!$submenu) return $fallback();

                return LaravelLocalization::getLocalizedURL(
                    $locale,
                    route('submenu.index', ['menu' => $getSlug($menu), 'submenu' => $getSlug($submenu)], false)
                );
            }

            // 3+ segment: menu/submenu/multimenu umumiy qismi
            [$menuSlug, $submenuSlug, $multimenuSlug] = $parts;
            $menu = $resolve($Menu, $menuSlug);
            if (!$menu) return $fallback();

            $submenu = $resolve($Submenu, $submenuSlug, ['menu_id' => $menu->id]);
            if (!$submenu) return $fallback();

            $multimenu = $resolve($Multimenu, $multimenuSlug, [
                'menu_id' => $menu->id,
                'submenu_id' => $submenu->id,
            ]);
            if (!$multimenu) return $fallback();

            // 3 segment: /menu/submenu/multimenu
            if (count($parts) === 3) {
                return LaravelLocalization::getLocalizedURL($locale, route('pages.show', [
                    'menu'      => $getSlug($menu),
                    'submenu'   => $getSlug($submenu),
                    'multimenu' => $getSlug($multimenu),
                ], false));
            }

            // 4 segment: /menu/submenu/multimenu/{page}
            if (count($parts) === 4) {
                $page = $findPage($menu->id, $submenu->id, $multimenu->id, $parts[3]);
                if (!$page) return $fallback();

                return LaravelLocalization::getLocalizedURL($locale, route('pages.detail', [
                    'menu'      => $getSlug($menu),
                    'submenu'   => $getSlug($submenu),
                    'multimenu' => $getSlug($multimenu),
                    'page'      => $pageTargetSlug($page),
                ], false));
            }

            // 5 segment: /menu/submenu/multimenu/staff/{id}
            if (count($parts) === 5 && $parts[3] === 'staff') {
                return LaravelLocalization::getLocalizedURL($locale, route('staff.show.simple', [
                    'menu'      => $getSlug($menu),
                    'submenu'   => $getSlug($submenu),
                    'multimenu' => $getSlug($multimenu),
                    'staff'     => $parts[4],
                ], false));
            }

            // 6 segment: /menu/submenu/multimenu/{page}/staff/{id}
            if (count($parts) === 6 && $parts[4] === 'staff') {
                $page = $findPage($menu->id, $submenu->id, $multimenu->id, $parts[3]);
                if (!$page) return $fallback();

                return LaravelLocalization::getLocalizedURL($locale, route('staff.show.withPage', [
                    'menu'      => $getSlug($menu),
                    'submenu'   => $getSlug($submenu),
                    'multimenu' => $getSlug($multimenu),
                    'page'      => $pageTargetSlug($page),
                    'staff'     => $parts[5],
                ], false));
            }

            return $fallback();
        } catch (\Throwable) {
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
            $pageSlug = $page->{$slugCol};

            if (empty($pageSlug)) {
                $pageSlug = $page->slug_uz;
            }

            if (empty($pageSlug)) {
                $pageSlug = $page->{'title_' . $locale}
                    ? Str::slug($page->{'title_' . $locale}) . '-' . $page->id
                    : (string) $page->id;
            }
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

        // Staff ID (model bo'lsa id, bo'lmasa integerga cast)
        $staffId = is_object($staff) ? $staff->id : (int) $staff;

        if ($page) {
            // 6 segment: /menu/submenu/multimenu/{page}/staff/{staff}
            $pageSlug = $page->{$slugCol};

            if (empty($pageSlug)) {
                $pageSlug = $page->slug_uz;
            }

            if (empty($pageSlug)) {
                $pageSlug = ($page->{'title_' . $locale} ?? null)
                    ? Str::slug($page->{'title_' . $locale}) . '-' . $page->id
                    : (string) $page->id;
            }

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
     * Agar mavjud bo'lmasa → uz tilidagi qiymatni qaytaradi.
     *
     * @param  object      $model   - Eloquent model (Page, StaffMember va h.k.)
     * @param  string      $base    - "title", "name" yoki "content"
     * @param  string|null $locale  - kerakli til (agar null bo'lsa app()->getLocale())
     * @return string|null
     */
    function localized_field(object $model, string $base, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        $field    = $base . '_' . $locale;
        $fallback = $base . '_uz';

        // !empty() o'rniga aniq tekshiruv: "0" ham to'g'ri qiymat
        $value = $model->{$field} ?? null;
        if ($value !== null && $value !== '') {
            return $value;
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
        $content = localized_field($model, 'content', $locale);

        // Mixed content oldini olish: http:// → https://
        if ($content && app()->isProduction()) {
            $host = parse_url(config('app.url'), PHP_URL_HOST) ?? '';
            if ($host) {
                $content = str_replace("http://{$host}", "https://{$host}", $content);
            }
        }

        return $content;
    }
}

if (! function_exists('lc_position')) {
    function lc_position(object $model, ?string $locale = null): ?string
    {
        return localized_field($model, 'position', $locale);
    }
}
