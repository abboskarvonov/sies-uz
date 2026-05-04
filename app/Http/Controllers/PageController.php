<?php

namespace App\Http\Controllers;

use App\Jobs\IncrementPageViews;
use App\Models\Menu;
use App\Models\Multimenu;
use App\Models\Page;
use App\Models\Submenu;
use App\Models\User;
use App\Models\UserPagePosition;
use App\Models\Tag;
use App\Services\HomepageService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function __construct(
        private readonly HomepageService $homepageService
    ) {}

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
            ? Cache::remember($cacheKey, $cacheTTL, fn() => $this->homepageService->getHomePageData($locale))
            : $this->homepageService->getHomePageData($locale);

        $data = $this->injectFreshViews($data);

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
            ->with('media')
            ->orderBy('order')
            ->paginate(config('site.pagination.per_page', 9));

        return view('pages.menu.index', [
            'menuModel' => $menuModel,
            'submenus' => $submenus,
            'metaTitle' => $menuModel->{"title_{$locale}"} ?? $menuModel->title_uz ?? 'Menu',
            'metaDescription' => Str::limit(strip_tags($menuModel->{"title_{$locale}"} ?? ''), config('site.meta.description_limit', 150)),
            'metaImage' => $menuModel->imageUrl() ?: asset(config('site.meta.default_image', 'img/og-image.webp')),
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
            ->with('media')
            ->orderBy('order')
            ->orderBy('id')
            ->paginate(config('site.pagination.per_page', 9));

        return view('pages.submenu.index', [
            'menuModel' => $menuModel,
            'submenuModel' => $submenuModel,
            'multimenus' => $multimenus,
            'metaTitle' => $submenuModel->{"title_{$locale}"} ?? $submenuModel->title_uz ?? 'Submenu',
            'metaDescription' => Str::limit(strip_tags($submenuModel->{"title_{$locale}"} ?? ''), config('site.meta.description_limit', 150)),
            'metaImage' => $submenuModel->imageUrl() ?: asset(config('site.meta.default_image', 'img/og-image.webp')),
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
        $baseQuery = fn($q) => $q
            ->where(function ($inner) use ($menuModel, $submenuModel, $multimenuId) {
                $inner->where([
                    'menu_id' => $menuModel->id,
                    'submenu_id' => $submenuModel->id,
                    'multimenu_id' => $multimenuId,
                ]);
            })
            ->orWhereHas('multimenus', fn($q) => $q->where('multimenus.id', $multimenuId));

        // page_type ni aniqlash uchun minimal query
        $pageType = Page::where($baseQuery)->value('page_type');

        if (!$pageType) {
            return view('pages.updating', compact('menuModel', 'submenuModel', 'multimenuModel'));
        }

        // List view uchun (blog, faculty, department)
        if (in_array($pageType, ['blog', 'faculty', 'department'])) {
            $pages = Page::where($baseQuery)
                ->select(['id', 'title_uz', 'title_ru', 'title_en', 'slug_uz', 'slug_ru', 'slug_en', 'content_uz', 'content_ru', 'content_en', 'image', 'date', 'views', 'menu_id', 'submenu_id', 'multimenu_id'])
                ->with('media')
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->paginate(config('site.pagination.per_page', 9));

            return view("pages.{$pageType}.index", [
                'pages' => $pages,
                'menuModel' => $menuModel,
                'submenuModel' => $submenuModel,
                'multimenuModel' => $multimenuModel,
                'metaTitle' => $multimenuModel->{"title_{$locale}"},
                'metaImage' => $multimenuModel->imageUrl() ?: asset(config('site.meta.default_image', 'img/og-image.webp')),
            ]);
        }

        // Single page — to'liq ma'lumotni bitta query da olamiz (type allaqachon ma'lum)
        $staffRelations = in_array($pageType, ['faculty', 'department', 'center', 'section'])
            ? $this->staffCategoryEagerLoad()
            : [];

        $page = Page::where($baseQuery)
            ->with(['files', ...$staffRelations])
            ->first();

        $this->incrementViewOnce($page);

        return view("pages.{$pageType}", [
            'page' => $page,
            'menuModel' => $menuModel,
            'submenuModel' => $submenuModel,
            'multimenuModel' => $multimenuModel,
            'locale' => $locale,
            'metaTitle' => $page->{"title_{$locale}"},
            'metaDescription' => Str::limit(strip_tags($page->{"content_{$locale}"}), config('site.meta.description_limit', 150)),
            'metaImage' => $page->imageUrl() ?: asset(config('site.meta.default_image', 'img/og-image.webp')),
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
                ->orWhereHas('multimenus', fn($mq) => $mq->where('multimenus.id', $multimenuId));
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
                ...$this->staffCategoryEagerLoad(),
                'departmentHistory:id,department_id,content_uz,content_ru,content_en',
            ])
            ->firstOrFail();

        $this->incrementViewOnce($pageModel);

        // Fakultet uchun tegishli kafedralarni yuklash
        if ($pageModel->page_type === 'faculty') {
            $pageModel->load([
                'childPages' => fn($q) => $q
                    ->where('page_type', 'department')
                    ->with(['menu:id,slug_uz,slug_ru,slug_en', 'submenu:id,slug_uz,slug_ru,slug_en', 'multimenu:id,slug_uz,slug_ru,slug_en'])
                    ->withCount('employees')
                    ->orderBy('order'),
            ]);
        }

        $view = match ($pageModel->page_type) {
            'blog' => 'pages.blog.show',
            'faculty' => 'pages.faculty.show',
            'department' => 'pages.department.show',
            'center' => 'pages.center',
            'section' => 'pages.section',
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
            'metaImage' => $pageModel->imageUrl() ?: asset(config('site.meta.default_image', 'img/og-image.webp')),
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

        [$staffModel, $contextPosition] = $this->findStaffInPage($pageModel->id, (int) $staff);

        return $this->renderStaffView($menuModel, $submenuModel, $multimenuModel, $pageModel, $staffModel, $locale, $contextPosition);
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

        [$staffModel, $contextPosition] = $this->findStaffInPage($pageModel->id, (int) $staff);

        return $this->renderStaffView($menuModel, $submenuModel, $multimenuModel, $pageModel, $staffModel, $locale, $contextPosition);
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
            ->with(['tags:id,name,slug', 'media'])
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
     * Fetch fresh views counts from DB and inject into cached homepage collections.
     * One extra SELECT query per request — keeps views always up-to-date.
     */
    private function injectFreshViews(array $data): array
    {
        $keys = ['otherNews', 'announcements', 'announcementsWithActivity'];

        $ids = collect($keys)
            ->flatMap(fn($k) => isset($data[$k]) ? $data[$k]->pluck('id') : [])
            ->push($data['latestNews']?->id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($ids)) {
            return $data;
        }

        $freshViews = Page::whereIn('id', $ids)->pluck('views', 'id');

        if ($data['latestNews']) {
            $data['latestNews']->views = $freshViews->get($data['latestNews']->id, 0);
        }

        foreach ($keys as $key) {
            if (!isset($data[$key])) {
                continue;
            }
            $data[$key]->each(function ($item) use ($freshViews) {
                $item->views = $freshViews->get($item->id, 0);
            });
        }

        return $data;
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
     * Xodimni page_id + user_id bo'yicha qidirish.
     * user_page_positions orqali qidiradi — birlamchi va ikkilamchi lavozimlarda ishlaydi.
     * @return array{0: User, 1: UserPagePosition|null}
     */
    private function findStaffInPage(int $pageId, int $userId): array
    {
        $staffModel = User::query()
            ->where('id', $userId)
            ->where(function ($q) use ($pageId) {
                // user_page_positions orqali yoki eski usul (department_page_id)
                $q->whereHas('pagePositions', fn($pq) => $pq->where('page_id', $pageId))
                  ->orWhere('department_page_id', $pageId);
            })
            ->select(['id', 'department_page_id', 'name', 'content_uz', 'content_ru', 'content_en', 'profile_photo_path', 'position_uz', 'position_ru', 'position_en', 'hemis_id'])
            ->with(['pagePositions' => fn($q) => $q->with('page:id,title_uz,title_ru,title_en,slug_uz,slug_ru,slug_en,menu_id,submenu_id,multimenu_id')])
            ->firstOrFail();

        // Joriy sahifadagi lavozimi (kontekst-spetsifik)
        $contextPosition = $staffModel->pagePositions->firstWhere('page_id', $pageId);

        return [$staffModel, $contextPosition];
    }

    /**
     * staffCategories eager load spetsifikatsiyasi (ikkala joyda bir xil).
     * belongsToMany orqali pivot position ma'lumotlari ham olinadi.
     */
    private function staffCategoryEagerLoad(): array
    {
        $employeesWith = fn($q) => $q
            ->select('users.id', 'users.name', 'users.profile_photo_path',
                     'users.position_uz', 'users.position_ru', 'users.position_en',
                     'users.content_uz', 'users.content_ru', 'users.content_en');

        return [
            'staffCategories' => function ($query) use ($employeesWith) {
                $query->whereNull('parent_id')
                    ->select(['id', 'title_uz', 'title_ru', 'title_en', 'page_id', 'parent_id', 'order'])
                    ->orderBy('order')
                    ->with([
                        'employees'          => $employeesWith,
                        'children'           => fn($q) => $q->select(['id', 'title_uz', 'title_ru', 'title_en', 'parent_id', 'page_id', 'order'])->orderBy('order'),
                        'children.employees' => $employeesWith,
                    ]);
            },
        ];
    }

    /**
     * Render staff view - DRY principle
     */
    private function renderStaffView(
        Model $menuModel,
        Model $submenuModel,
        Model $multimenuModel,
        Model $pageModel,
        User $staffModel,
        string $locale,
        ?UserPagePosition $contextPosition = null
    ): View {
        return view('pages.staff.show', [
            'menuModel' => $menuModel,
            'submenuModel' => $submenuModel,
            'multimenuModel' => $multimenuModel,
            'pageModel' => in_array($pageModel->page_type, ['faculty', 'department']) ? $pageModel : null,
            'staff' => $staffModel,
            'contextPosition' => $contextPosition,
            'metaTitle' => $staffModel->name ?? 'Xodim',
            'metaDescription' => Str::limit(strip_tags($staffModel->{"content_{$locale}"} ?? ''), config('site.meta.description_limit', 150)),
            'metaImage' => $staffModel->profile_photo_path
                ? asset('storage/' . $staffModel->profile_photo_path)
                : asset(config('site.meta.default_image', 'img/og-image.webp')),
            'canonical' => url()->current(),
            'locale' => $locale,
        ]);
    }
}
