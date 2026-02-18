<?php

namespace App\Http\Controllers;

use App\Jobs\IncrementPageViews;
use App\Models\Menu;
use App\Models\Multimenu;
use App\Models\Page;
use App\Models\SiteStat;
use App\Models\StaffMember;
use App\Models\Submenu;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Home page - optimized with cache and query reduction
     */
    public function index(): View
    {
        $locale = app()->getLocale();

        // Cache key'ga locale qo'shildi
        $cacheKey = "homepage_data_{$locale}";
        $cacheTTL = config('site.cache.ttl.homepage', 3600);

        $data = config('site.cache.enabled')
            ? Cache::remember($cacheKey, $cacheTTL, fn() => $this->getHomePageData($locale))
            : $this->getHomePageData($locale);

        return view('pages.index', $data);
    }

    /**
     * Menu index page - optimized
     */
    public function menuIndex(string $menu): View
    {
        $locale = app()->getLocale();
        $slugCol = "slug_{$locale}";

        $menuModel = $this->findModelBySlug(Menu::class, $slugCol, $menu);

        $submenus = $menuModel->submenus()
            ->select(['id', 'menu_id', 'title_uz', 'title_ru', 'title_en', 'slug_uz', 'slug_ru', 'slug_en', 'image', 'order'])
            ->orderBy('order')
            ->paginate(config('site.pagination.per_page', 9));

        return view('pages.menu.index', [
            'menuModel' => $menuModel,
            'submenus' => $submenus,
            'metaTitle' => $menuModel->{"title_{$locale}"} ?? $menuModel->title_uz ?? 'Menu',
            'metaDescription' => Str::limit(strip_tags($menuModel->{"title_{$locale}"} ?? ''), config('site.meta.description_limit', 150)),
            'metaImage' => $menuModel->image
                ? asset('storage/' . $menuModel->image)
                : asset(config('site.meta.default_image', 'img/og-image.webp')),
            'canonical' => url()->current(),
        ]);
    }

    /**
     * Submenu index page - optimized
     */
    public function submenuIndex(string $menu, string $submenu): View
    {
        $locale = app()->getLocale();
        $slugCol = "slug_{$locale}";

        $menuModel = $this->findModelBySlug(Menu::class, $slugCol, $menu);
        $submenuModel = $this->findModelBySlug(Submenu::class, $slugCol, $submenu, ['menu_id' => $menuModel->id]);

        $multimenus = Multimenu::query()
            ->where('menu_id', $menuModel->id)
            ->where('submenu_id', $submenuModel->id)
            ->select(['id', 'menu_id', 'submenu_id', 'title_uz', 'title_ru', 'title_en', 'slug_uz', 'slug_ru', 'slug_en', 'image', 'order'])
            ->orderBy('order')
            ->orderBy('id')
            ->paginate(config('site.pagination.per_page', 9));

        return view('pages.submenu.index', [
            'menuModel' => $menuModel,
            'submenuModel' => $submenuModel,
            'multimenus' => $multimenus,
            'metaTitle' => $submenuModel->{"title_{$locale}"} ?? $submenuModel->title_uz ?? 'Submenu',
            'metaDescription' => Str::limit(strip_tags($submenuModel->{"title_{$locale}"} ?? ''), config('site.meta.description_limit', 150)),
            'metaImage' => $submenuModel->image
                ? asset('storage/' . $submenuModel->image)
                : asset(config('site.meta.default_image', 'img/og-image.webp')),
            'canonical' => url()->current(),
        ]);
    }

    /**
     * Pages show - optimized with lazy loading
     */
    public function pagesShow(string $menu, ?string $submenu = null, ?string $multimenu = null): View
    {
        $locale = app()->getLocale();
        $slugCol = "slug_{$locale}";

        $menuModel = $this->findModelBySlug(Menu::class, $slugCol, $menu);
        $submenuModel = $this->findModelBySlug(Submenu::class, $slugCol, $submenu, ['menu_id' => $menuModel->id]);
        $multimenuModel = $this->findModelBySlug(Multimenu::class, $slugCol, $multimenu, ['submenu_id' => $submenuModel->id]);

        // Multimenu bo'yicha sahifalarni olish (asosiy multimenu_id YOKI pivot orqali biriktirilgan)
        $multimenuId = $multimenuModel->id;
        $pageScope = fn ($query) => $query
            ->where(function ($q) use ($menuModel, $submenuModel, $multimenuId) {
                $q->where([
                    'menu_id' => $menuModel->id,
                    'submenu_id' => $submenuModel->id,
                    'multimenu_id' => $multimenuId,
                ]);
            })
            ->orWhereHas('multimenus', fn ($q) => $q->where('multimenus.id', $multimenuId));

        // Minimal select bilan birinchi page'ni olish
        $firstPage = Page::where(function ($query) use ($pageScope) {
                $pageScope($query);
            })
            ->select(['id', 'page_type', 'title_uz', 'title_ru', 'title_en', 'content_uz', 'content_ru', 'content_en', 'date', 'image', 'views', 'images'])
            ->first();

        if (!$firstPage) {
            return view('pages.updating', compact('menuModel', 'submenuModel', 'multimenuModel'));
        }

        $pageType = $firstPage->page_type;

        // List view uchun (blog, faculty, department)
        if (in_array($pageType, ['blog', 'faculty', 'department'])) {
            $pages = Page::where(function ($query) use ($pageScope) {
                    $pageScope($query);
                })
                ->select(['id', 'title_uz', 'title_ru', 'title_en', 'slug_uz', 'slug_ru', 'slug_en', 'content_uz', 'content_ru', 'content_en', 'image', 'date', 'views', 'menu_id', 'submenu_id', 'multimenu_id'])
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->paginate(config('site.pagination.per_page', 9));

            return view("pages.{$pageType}.index", [
                'pages' => $pages,
                'menuModel' => $menuModel,
                'submenuModel' => $submenuModel,
                'multimenuModel' => $multimenuModel,
                'metaTitle' => $multimenuModel->{"title_{$locale}"},
                'metaImage' => $multimenuModel->image
                    ? asset('storage/' . $multimenuModel->image)
                    : asset(config('site.meta.default_image', 'img/og-image.webp')),
            ]);
        }

        // Single page view uchun
        $page = Page::where(function ($query) use ($pageScope) {
                $pageScope($query);
            })
            ->with(['files'])
            ->first();

        // Lazy load - faqat kerak bo'lsa
        if (in_array($pageType, ['center', 'section'])) {
            $page->load([
                'staffCategories' => function ($query) {
                    $query->whereNull('parent_id')
                        ->select(['id', 'title_uz', 'title_ru', 'title_en', 'page_id', 'parent_id'])
                        ->with([
                            'staffMembers:id,name_uz,name_ru,name_en,position_uz,position_ru,position_en,image,staff_category_id,page_id',
                            'children.staffMembers:id,name_uz,name_ru,name_en,position_uz,position_ru,position_en,image,staff_category_id,page_id',
                        ]);
                },
            ]);
        }

        $this->incrementViewOnce($page);

        return view("pages.{$pageType}", [
            'page' => $page,
            'menuModel' => $menuModel,
            'submenuModel' => $submenuModel,
            'multimenuModel' => $multimenuModel,
            'locale' => $locale,
            'metaTitle' => $page->{"title_{$locale}"},
            'metaDescription' => Str::limit(strip_tags($page->{"content_{$locale}"}), config('site.meta.description_limit', 150)),
            'metaImage' => $page->image
                ? asset('storage/' . $page->image)
                : asset(config('site.meta.default_image', 'img/og-image.webp')),
        ]);
    }

    /**
     * Page detail - optimized
     */
    public function pageDetail(string $menu, string $submenu, string $multimenu, string $page): View
    {
        $locale = app()->getLocale();
        $slugCol = "slug_{$locale}";

        $menuModel = $this->findModelBySlug(Menu::class, $slugCol, $menu);
        $submenuModel = $this->findModelBySlug(Submenu::class, $slugCol, $submenu, ['menu_id' => $menuModel->id]);
        $multimenuModel = $this->findModelBySlug(Multimenu::class, $slugCol, $multimenu, ['submenu_id' => $submenuModel->id]);

        $multimenuId = $multimenuModel->id;

        $pageModel = Page::where(function ($q) use ($menuModel, $submenuModel, $multimenuId) {
                $q->where([
                    'menu_id' => $menuModel->id,
                    'submenu_id' => $submenuModel->id,
                    'multimenu_id' => $multimenuId,
                ])
                ->orWhereHas('multimenus', fn ($mq) => $mq->where('multimenus.id', $multimenuId));
            })
            ->where(function ($q) use ($slugCol, $page) {
                // Avval slug bo'yicha qidirish
                $q->where($slugCol, $page);

                // Agar slug-id formatida bo'lsa (masalan: "yangilik-123")
                $id = (int) preg_replace('/^.*-(\d+)$/', '$1', $page);
                if ($id > 0) {
                    $q->orWhere('id', $id);
                }

                // Agar faqat ID bo'lsa (masalan: "123")
                if (is_numeric($page)) {
                    $q->orWhere('id', (int) $page);
                }
            })
            ->with([
                'files:id,page_id,name,file,created_at,updated_at',
                'staffCategories' => function ($query) {
                    $query->whereNull('parent_id')
                        ->select(['id', 'title_uz', 'title_ru', 'title_en', 'page_id', 'parent_id'])
                        ->with([
                            'staffMembers:id,name_uz,name_ru,name_en,position_uz,position_ru,position_en,image,staff_category_id,page_id',
                            'children:id,title_uz,title_ru,title_en,parent_id,page_id',
                            'children.staffMembers:id,name_uz,name_ru,name_en,position_uz,position_ru,position_en,image,staff_category_id,page_id',
                        ]);
                },
                'departmentHistory:id,department_id,content_uz,content_ru,content_en',
            ])
            ->firstOrFail();

        $this->incrementViewOnce($pageModel);

        $view = match ($pageModel->page_type) {
            'blog' => 'pages.blog.show',
            'faculty' => 'pages.faculty.show',
            'department' => 'pages.department.show',
            default => 'pages.default',
        };

        // Images processing - optimized
        $images = $this->processPageImages($pageModel->images);

        return view($view, [
            'locale' => $locale,
            'page' => $pageModel,
            'menuModel' => $menuModel,
            'submenuModel' => $submenuModel,
            'multimenuModel' => $multimenuModel,
            'images' => $images,
            'metaTitle' => $pageModel->{"title_{$locale}"},
            'metaDescription' => Str::limit(strip_tags($pageModel->{"content_{$locale}"}), config('site.meta.description_limit', 150)),
            'metaImage' => $pageModel->image
                ? asset('storage/' . $pageModel->image)
                : asset(config('site.meta.default_image', 'img/og-image.webp')),
        ]);
    }

    /**
     * Staff show with page - optimized and refactored
     */
    public function staffShowWithPage(string $menu, string $submenu, string $multimenu, string $page, string $staff): View
    {
        $locale = app()->getLocale();
        $slugCol = "slug_{$locale}";

        $menuModel = $this->findModelBySlug(Menu::class, $slugCol, $menu);
        $submenuModel = $this->findModelBySlug(Submenu::class, $slugCol, $submenu, ['menu_id' => $menuModel->id]);
        $multimenuModel = $this->findModelBySlug(Multimenu::class, $slugCol, $multimenu, [
            'menu_id' => $menuModel->id,
            'submenu_id' => $submenuModel->id,
        ]);

        $pageModel = Page::query()
            ->where('menu_id', $menuModel->id)
            ->where('submenu_id', $submenuModel->id)
            ->where('multimenu_id', $multimenuModel->id)
            ->where(function ($q) use ($slugCol, $page) {
                $q->where($slugCol, $page);
                $id = (int) preg_replace('/^.*-(\d+)$/', '$1', $page);
                if ($id > 0) {
                    $q->orWhere('id', $id);
                }
            })
            ->select(['id', 'page_type', 'title_uz', 'title_ru', 'title_en', 'menu_id', 'submenu_id', 'multimenu_id'])
            ->firstOrFail();

        $staffModel = StaffMember::query()
            ->where('page_id', $pageModel->id)
            ->where('id', (int) $staff)
            ->select(['id', 'page_id', 'name_uz', 'name_ru', 'name_en', 'content_uz', 'content_ru', 'content_en', 'image', 'position_uz', 'position_ru', 'position_en'])
            ->firstOrFail();

        return $this->renderStaffView($menuModel, $submenuModel, $multimenuModel, $pageModel, $staffModel, $locale);
    }

    /**
     * Staff show simple - optimized
     */
    public function staffShowSimple(string $menu, string $submenu, string $multimenu, string $staff): View
    {
        $locale = app()->getLocale();
        $slugCol = "slug_{$locale}";

        $menuModel = $this->findModelBySlug(Menu::class, $slugCol, $menu);
        $submenuModel = $this->findModelBySlug(Submenu::class, $slugCol, $submenu, ['menu_id' => $menuModel->id]);
        $multimenuModel = $this->findModelBySlug(Multimenu::class, $slugCol, $multimenu, [
            'menu_id' => $menuModel->id,
            'submenu_id' => $submenuModel->id,
        ]);

        $pageModel = Page::query()
            ->where('menu_id', $menuModel->id)
            ->where('submenu_id', $submenuModel->id)
            ->where('multimenu_id', $multimenuModel->id)
            ->select(['id', 'page_type', 'title_uz', 'title_ru', 'title_en', 'menu_id', 'submenu_id', 'multimenu_id'])
            ->firstOrFail();

        $staffModel = StaffMember::query()
            ->where('page_id', $pageModel->id)
            ->where('id', (int) $staff)
            ->select(['id', 'page_id', 'name_uz', 'name_ru', 'name_en', 'content_uz', 'content_ru', 'content_en', 'image', 'position_uz', 'position_ru', 'position_en'])
            ->firstOrFail();

        return $this->renderStaffView($menuModel, $submenuModel, $multimenuModel, $pageModel, $staffModel, $locale);
    }

    /**
     * Tags index - cached
     */
    public function tagsIndex(): View
    {
        $cacheKey = 'tags_index_' . app()->getLocale();
        $cacheTTL = config('site.cache.ttl.tags', 7200);

        $tags = config('site.cache.enabled')
            ? Cache::remember($cacheKey, $cacheTTL, fn() => Tag::select(['id', 'name', 'slug'])->get())
            : Tag::select(['id', 'name', 'slug'])->get();

        return view('pages.tags.index', [
            'tags' => $tags,
            'metaTitle' => __('Teglar'),
            'metaDescription' => __('Veb-saytimizdagi barcha teglar ro\'yxati'),
            'metaImage' => asset(config('site.meta.default_image', 'img/og-image.webp')),
        ]);
    }

    /**
     * Tags show - optimized
     */
    public function tagsShow(string $slug): View
    {
        $tag = Tag::where('slug', $slug)
            ->select(['id', 'name', 'slug'])
            ->firstOrFail();

        $pages = Page::query()
            ->whereHas('tags', fn($q) => $q->where('tags.id', $tag->id))
            ->with(['tags:id,name,slug'])
            ->select(['id', 'title_uz', 'title_ru', 'title_en', 'slug_uz', 'slug_ru', 'slug_en', 'content_uz', 'content_ru', 'content_en', 'image', 'date', 'views', 'menu_id', 'submenu_id', 'multimenu_id'])
            ->latest('date')
            ->paginate(config('site.pagination.per_page', 9));

        return view('pages.tags.show', [
            'tag' => $tag,
            'pages' => $pages,
            'metaTitle' => __('Teg') . ': ' . $tag->name,
            'metaDescription' => __('":name" tegiga biriktirilgan sahifalar', ['name' => $tag->name]),
            'metaImage' => asset(config('site.meta.default_image', 'img/og-image.webp')),
        ]);
    }

    /**
     * =================================================================
     * PRIVATE HELPER METHODS - Optimized and Refactored
     * =================================================================
     */

    /**
     * Get home page data - optimized with reduced queries
     */
    private function getHomePageData(string $locale): array
    {
        $newsConfig = config('site.menus.news');
        $announcementsConfig = config('site.menus.announcements');

        // News pages - alohida query (optimized)
        $newsPages = Page::ofType('blog')
            ->inMenu($newsConfig['menu_id'], $newsConfig['submenu_id'], $newsConfig['multimenu_id'])
            ->select(['id', 'title_uz', 'title_ru', 'title_en', 'slug_uz', 'slug_ru', 'slug_en', 'content_uz', 'content_ru', 'content_en', 'image', 'date', 'views', 'menu_id', 'submenu_id', 'multimenu_id'])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->take(10)
            ->get();

        // Gallery uchun alohida query (images bilan)
        $galleryPages = Page::ofType('blog')
            ->inMenu($newsConfig['menu_id'], $newsConfig['submenu_id'], $newsConfig['multimenu_id'])
            ->whereNotNull('images')
            ->where('images', '!=', '[]')
            ->where('images', '!=', 'null')
            ->select(['images'])
            ->orderByDesc('date')
            ->take(10)
            ->get();

        // Announcements - alohida query (optimized)
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

        // Gallery images - optimized with proper casting
        $galleryImages = $galleryPages
            ->flatMap(function ($item) {
                $images = is_array($item->images) ? $item->images : json_decode($item->images, true);
                if (!is_array($images)) {
                    return [];
                }
                return collect($images)->map(fn($img) => asset('storage/' . $img));
            })
            ->shuffle()
            ->take(config('site.pagination.home.gallery_images', 12));

        // Parallel queries
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
            'id',
            'campus_area',
            'green_area',
            'faculties',
            'departments',
            'centers',
            'employees',
            'leadership',
            'scientific',
            'technical',
            'students',
            'male_students',
            'female_students',
            'teachers',
            'dsi',
            'phd_teachers',
            'professors',
            'books',
            'textbooks',
            'study',
            'methodological',
            'monograph'
        ])->first();

        return compact(
            'latestNews',
            'otherNews',
            'announcements',
            'announcementsWithActivity',
            'faculties',
            'departments',
            'galleryImages',
            'tags',
            'stat'
        );
    }

    /**
     * Generic model finder - DRY principle
     */
    private function findModelBySlug(string $modelClass, string $slugColumn, string $slug, array $where = []): Model
    {
        return $modelClass::where($slugColumn, $slug)
            ->where($where)
            ->firstOrFail();
    }

    /**
     * Increment page views - optimized with multiple strategies
     */
    private function incrementViewOnce(Page $page): void
    {
        if (!config('site.view_tracking.enabled', true)) {
            return;
        }

        $method = config('site.view_tracking.method', 'cache');
        $cacheKey = "page_viewed_{$page->id}_" . request()->ip();
        $ttl = config('site.view_tracking.ttl', 3600);

        if ($method === 'queue') {
            // Async via queue (best for high traffic)
            if (!Cache::has($cacheKey)) {
                dispatch(new IncrementPageViews($page->id));
                Cache::put($cacheKey, true, $ttl);
            }
        } elseif ($method === 'cache') {
            // Cache-based (better than session)
            if (!Cache::has($cacheKey)) {
                $page->increment('views');
                Cache::put($cacheKey, true, $ttl);
            }
        } else {
            // Session-based (fallback)
            $sessionKey = "page_viewed_{$page->id}";
            if (!session()->has($sessionKey)) {
                $page->increment('views');
                session()->put($sessionKey, true);
            }
        }
    }

    /**
     * Process page images - extracted for reusability
     */
    private function processPageImages($raw): array
    {
        $images = [];

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $decoded = json_decode(stripslashes($raw), true);
            }
            if (is_array($decoded)) {
                $images = array_values(array_filter($decoded));
            }
        } elseif (is_array($raw)) {
            $images = array_values(array_filter($raw));
        }

        return array_map(function ($p) {
            $p = ltrim($p, '/');
            if (Str::startsWith($p, ['http://', 'https://'])) {
                return $p;
            }
            if (Str::startsWith($p, 'storage/')) {
                return asset($p);
            }
            return asset('storage/' . $p);
        }, $images);
    }

    /**
     * Render staff view - DRY principle
     */
    private function renderStaffView(
        Model $menuModel,
        Model $submenuModel,
        Model $multimenuModel,
        Model $pageModel,
        StaffMember $staffModel,
        string $locale
    ): View {
        return view('pages.staff.show', [
            'menuModel' => $menuModel,
            'submenuModel' => $submenuModel,
            'multimenuModel' => $multimenuModel,
            'pageModel' => in_array($pageModel->page_type, ['faculty', 'department']) ? $pageModel : null,
            'staff' => $staffModel,
            'metaTitle' => $staffModel->{"name_{$locale}"} ?? 'Xodim',
            'metaDescription' => Str::limit(strip_tags($staffModel->{"content_{$locale}"} ?? ''), config('site.meta.description_limit', 150)),
            'metaImage' => $staffModel->image
                ? asset('storage/' . $staffModel->image)
                : asset(config('site.meta.default_image', 'img/og-image.webp')),
            'canonical' => url()->current(),
            'locale' => $locale,
        ]);
    }
}
