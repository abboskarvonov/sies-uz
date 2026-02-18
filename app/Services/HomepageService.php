<?php

namespace App\Services;

use App\Models\Page;
use App\Models\SiteStat;
use App\Models\Tag;

class HomepageService
{
    public function getHomePageData(string $locale): array
    {
        $newsConfig = config('site.menus.news');
        $announcementsConfig = config('site.menus.announcements');

        $newsPages = Page::ofType('blog')
            ->inMenu($newsConfig['menu_id'], $newsConfig['submenu_id'], $newsConfig['multimenu_id'])
            ->select(['id', 'title_uz', 'title_ru', 'title_en', 'slug_uz', 'slug_ru', 'slug_en', 'content_uz', 'content_ru', 'content_en', 'image', 'date', 'views', 'menu_id', 'submenu_id', 'multimenu_id'])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->take(10)
            ->get();

        $galleryPages = Page::ofType('blog')
            ->inMenu($newsConfig['menu_id'], $newsConfig['submenu_id'], $newsConfig['multimenu_id'])
            ->whereNotNull('images')
            ->where('images', '!=', '[]')
            ->where('images', '!=', 'null')
            ->select(['images'])
            ->orderByDesc('date')
            ->take(10)
            ->get();

        $announcementPages = Page::ofType('blog')
            ->inMenu($announcementsConfig['menu_id'], $announcementsConfig['submenu_id'], $announcementsConfig['multimenu_id'])
            ->select(['id', 'title_uz', 'title_ru', 'title_en', 'slug_uz', 'slug_ru', 'slug_en', 'content_uz', 'content_ru', 'content_en', 'image', 'date', 'views', 'activity', 'menu_id', 'submenu_id', 'multimenu_id'])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->take(11)
            ->get();

        $latestNews = $newsPages->first();
        $otherNews = $newsPages->skip(1)->take(config('site.pagination.home.other_news', 6));
        $announcements = $announcementPages->take(config('site.pagination.home.announcements', 11));
        $announcementsWithActivity = $announcementPages->where('activity', true)
            ->take(config('site.pagination.home.announcements_with_activity', 6));

        $galleryImages = $galleryPages
            ->flatMap(function ($item) {
                $images = is_array($item->images) ? $item->images : json_decode($item->images, true);
                if (!is_array($images)) return [];
                return collect($images)->map(fn($img) => asset('storage/' . $img));
            })
            ->shuffle()
            ->take(config('site.pagination.home.gallery_images', 12));

        $faculties = Page::ofType('faculty')
            ->select(['id', 'title_uz', 'title_ru', 'title_en', 'slug_uz', 'slug_ru', 'slug_en', 'content_uz', 'content_ru', 'content_en', 'image', 'date', 'menu_id', 'submenu_id', 'multimenu_id'])
            ->orderBy('date')
            ->take(config('site.pagination.home.faculties', 4))
            ->get();

        $departments = Page::ofType('department')
            ->select(['id', 'title_uz', 'title_ru', 'title_en', 'slug_uz', 'slug_ru', 'slug_en', 'content_uz', 'content_ru', 'content_en', 'image', 'date', 'menu_id', 'submenu_id', 'multimenu_id'])
            ->orderBy('date')
            ->take(config('site.pagination.home.departments', 6))
            ->get();

        $tags = Tag::select(['id', 'name', 'slug'])->get();
        $stat = SiteStat::select([
            'id', 'campus_area', 'green_area', 'faculties', 'departments', 'centers',
            'employees', 'leadership', 'scientific', 'technical',
            'students', 'male_students', 'female_students', 'teachers',
            'dsi', 'phd_teachers', 'professors',
            'books', 'textbooks', 'study', 'methodological', 'monograph'
        ])->first();

        return compact(
            'latestNews', 'otherNews', 'announcements', 'announcementsWithActivity',
            'faculties', 'departments', 'galleryImages', 'tags', 'stat'
        );
    }
}
