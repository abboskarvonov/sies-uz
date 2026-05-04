<?php

namespace App\Console\Commands;

use App\Models\Menu;
use App\Models\Multimenu;
use App\Models\Page;
use App\Models\Submenu;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate XML sitemaps';

    public function handle(): int
    {
        $this->info('Generating sitemap…');

        // Agar sayt katta bo‘lsa, sitemaps ni bo‘lib (index bilan) chiqaramiz:
        $index = SitemapIndex::create();

        // 1) Statik sahifalar (bosh sahifa va h.k.)
        $staticMapPath = public_path('sitemap-static.xml');
        $static = Sitemap::create();

        // Bosh sahifa
        $static->add(
            Url::create(url('/'))
                ->setLastModificationDate(Carbon::now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0)
        );

        // Agar boshqa statik sahifalar bo‘lsa shu yerga qo‘shing:
        // $static->add(Url::create(url('/contact')) ...);

        $static->writeToFile($staticMapPath);
        $index->add(url('sitemap-static.xml'));

        // Til kodlari (mcamara/laravel-localization’dan)
        $locales = array_keys(LaravelLocalization::getSupportedLocales() ?? ['uz' => []]);

        // 2) Katalog sahifalari: menu, submenu, multimenu ro‘yxatlari (paginasiz)
        $catalogMapPath = public_path('sitemap-catalog.xml');
        $catalog = Sitemap::create();

        // Menu / Submenu / Multimenu — har bir til uchun URL
        Menu::query()->orderBy('id')->chunk(500, function ($menus) use ($catalog, $locales) {
            foreach ($menus as $menu) {
                foreach ($locales as $loc) {
                    $slug = $menu->{'slug_' . $loc} ?? $menu->slug_uz;
                    if (!$slug) continue;

                    $catalog->add(
                        Url::create(url("$loc/$slug"))
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.7)
                    );
                }
            }
        });

        Submenu::query()->orderBy('id')->chunk(500, function ($subs) use ($catalog, $locales) {
            foreach ($subs as $submenu) {
                foreach ($locales as $loc) {
                    $menuSlug    = optional($submenu->menu)->{'slug_' . $loc}    ?? optional($submenu->menu)->slug_uz;
                    $submenuSlug = $submenu->{'slug_' . $loc} ?? $submenu->slug_uz;
                    if (!$menuSlug || !$submenuSlug) continue;

                    $catalog->add(
                        Url::create(url("$loc/$menuSlug/$submenuSlug"))
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.7)
                    );
                }
            }
        });

        Multimenu::query()->with(['menu', 'submenu'])->orderBy('id')->chunk(500, function ($multis) use ($catalog, $locales) {
            foreach ($multis as $multi) {
                foreach ($locales as $loc) {
                    $menuSlug     = optional($multi->menu)->{'slug_' . $loc}     ?? optional($multi->menu)->slug_uz;
                    $submenuSlug  = optional($multi->submenu)->{'slug_' . $loc}  ?? optional($multi->submenu)->slug_uz;
                    $multiSlug    = $multi->{'slug_' . $loc} ?? $multi->slug_uz;
                    if (!$menuSlug || !$submenuSlug || !$multiSlug) continue;

                    $catalog->add(
                        Url::create(url("$loc/$menuSlug/$submenuSlug/$multiSlug"))
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                            ->setPriority(0.8)
                    );
                }
            }
        });

        $catalog->writeToFile($catalogMapPath);
        $index->add(url('sitemap-catalog.xml'));

        // 3) Dinamik sahifalar (Pages) — blog/faculty/department/default… hammasi
        // Juda ko‘p bo‘lsa, bo‘lib yozamiz: sitemap-pages-1.xml, sitemap-pages-2.xml, …
        $pageChunk = 3000; // Google limit ~50k, lekin fayl hajmini (50MB) yodda tuting
        $counter   = 1;

        Page::query()
            ->where('status', 'active')
            ->with(['menu', 'submenu', 'multimenu', 'media'])
            ->orderBy('updated_at', 'desc')
            ->chunk($pageChunk, function ($pages) use (&$counter, $locales, $index) {
                $map = Sitemap::create();

                foreach ($pages as $page) {
                    // Eng ko‘p ishlatiladigan URL: /{lang}/{menu}/{submenu}/{multimenu} yoki detallarda /{…}/{page-slug}
                    foreach ($locales as $loc) {
                        $menuSlug     = optional($page->menu)->{'slug_' . $loc}     ?? optional($page->menu)->slug_uz;
                        $submenuSlug  = optional($page->submenu)->{'slug_' . $loc}  ?? optional($page->submenu)->slug_uz;
                        $multiSlug    = optional($page->multimenu)->{'slug_' . $loc} ?? optional($page->multimenu)->slug_uz;
                        if (!$menuSlug || !$submenuSlug || !$multiSlug) continue;

                        // Agar page turiga qarab detail kerak bo‘lsa:
                        $needsDetail = in_array($page->page_type, ['blog', 'faculty', 'department']);
                        $pageSlug    = $page->{'slug_' . $loc}
                            ?? (($page->{'title_' . $loc} ?? null) ? Str::slug($page->{'title_' . $loc}) . '-' . $page->id : (string)$page->id);

                        $url = $needsDetail
                            ? url("$loc/$menuSlug/$submenuSlug/$multiSlug/$pageSlug")
                            : url("$loc/$menuSlug/$submenuSlug/$multiSlug");

                        $tag = Url::create($url)
                            ->setLastModificationDate($page->updated_at ?? now())
                            ->setChangeFrequency($needsDetail ? Url::CHANGE_FREQUENCY_DAILY : Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority($needsDetail ? 0.9 : 0.8);

                        // Asosiy rasm
                        $mainImg = $page->imageUrl(‘webp’);
                        if ($mainImg) {
                            $tag->addImage($mainImg);
                        }

                        // Gallery rasmlari (birinchi 10 ta)
                        foreach (array_slice($page->galleryUrls(‘webp’), 0, 10) as $imgUrl) {
                            $tag->addImage($imgUrl);
                        }

                        $map->add($tag);
                    }
                }

                $filename = "sitemap-pages-{$counter}.xml";
                $map->writeToFile(public_path($filename));
                $index->add(url($filename));
                $counter++;
            });

        // Index faylini yozamiz:
        $index->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated: ' . url('sitemap.xml'));
        return self::SUCCESS;
    }
}