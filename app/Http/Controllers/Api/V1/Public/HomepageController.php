<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PageListResource;
use App\Http\Resources\Api\V1\SiteStatResource;
use App\Http\Resources\Api\V1\TagResource;
use App\Http\Traits\Api\ApiResponses;
use App\Http\Traits\Api\HasImageUrls;
use App\Models\Page;
use App\Models\SiteStat;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;

class HomepageController extends Controller
{
    use ApiResponses, HasImageUrls;

    public function index()
    {
        $locale = app()->getLocale();
        $cacheKey = "api:homepage_{$locale}";
        $ttl = config('site.cache.ttl.homepage', 3600);

        $data = config('site.cache.enabled')
            ? Cache::remember($cacheKey, $ttl, fn () => $this->getHomePageData())
            : $this->getHomePageData();

        return $this->successResponse($data);
    }

    private function getHomePageData(): array
    {
        $newsConfig = config('site.menus.news');
        $announcementsConfig = config('site.menus.announcements');

        $newsPages = Page::ofType('blog')
            ->inMenu($newsConfig['menu_id'], $newsConfig['submenu_id'], $newsConfig['multimenu_id'])
            ->with('tags:id,name,slug')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->take(10)
            ->get();

        $announcementPages = Page::ofType('blog')
            ->inMenu($announcementsConfig['menu_id'], $announcementsConfig['submenu_id'], $announcementsConfig['multimenu_id'])
            ->with('tags:id,name,slug')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->take(11)
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

        $galleryImages = $galleryPages->flatMap(function ($item) {
            $images = is_array($item->images) ? $item->images : json_decode($item->images, true);
            if (!is_array($images)) return [];
            return collect($images)->map(fn ($img) => $this->imageUrl($img));
        })->shuffle()->take(config('site.pagination.home.gallery_images', 12))->values();

        $faculties = Page::ofType('faculty')
            ->with('tags:id,name,slug')
            ->orderBy('date')
            ->take(config('site.pagination.home.faculties', 4))
            ->get();

        $departments = Page::ofType('department')
            ->with('tags:id,name,slug')
            ->orderBy('date')
            ->take(config('site.pagination.home.departments', 6))
            ->get();

        $tags = Tag::orderBy('order')->get();
        $stats = SiteStat::first();

        return [
            'latest_news' => $newsPages->first() ? new PageListResource($newsPages->first()) : null,
            'other_news' => PageListResource::collection($newsPages->skip(1)->take(config('site.pagination.home.other_news', 6))),
            'announcements' => PageListResource::collection($announcementPages->take(config('site.pagination.home.announcements', 11))),
            'announcements_with_activity' => PageListResource::collection(
                $announcementPages->where('activity', true)->take(config('site.pagination.home.announcements_with_activity', 6))
            ),
            'gallery_images' => $galleryImages,
            'faculties' => PageListResource::collection($faculties),
            'departments' => PageListResource::collection($departments),
            'tags' => TagResource::collection($tags),
            'stats' => $stats ? new SiteStatResource($stats) : null,
        ];
    }
}
