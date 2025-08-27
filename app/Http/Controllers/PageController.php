<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Multimenu;
use App\Models\Page;
use App\Models\SiteStat;
use App\Models\StaffMember;
use App\Models\Submenu;
use App\Models\Tag;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        // Eng oxirgi yangilik
        $latestNews = Page::ofType('blog')
            ->inMenu(1, 1, 1)
            ->latestByDate()
            ->first();

        // Keyingi 3 ta yangilik
        $otherNews = Page::ofType('blog')
            ->inMenu(1, 1, 1)
            ->where('id', '!=', optional($latestNews)->id)
            ->latestByDate()
            ->take(4)
            ->get();

        // E'lonlar
        $announcements = Page::ofType('blog')
            ->inMenu(1, 1, 2)
            ->latestByDate()
            ->take(9)
            ->get();

        // Ilmiy faoliyat e'lonlari
        $announcementsWithActivity = Page::ofType('blog')
            ->inMenu(1, 1, 2)
            ->where('activity', true)
            ->latestByDate()
            ->take(4)
            ->get();

        // Fakultetlar
        $faculties = Page::ofType('faculty')
            ->orderBy('date', 'asc')
            ->take(4)
            ->get();

        // Kafedralar
        $departments = Page::ofType('department')
            ->orderBy('date', 'asc')
            ->take(6)
            ->get();

        // Galereya rasmlari (oxirgi 10 newsdan)
        $galleryImages = Page::ofType('blog')
            ->inMenu(1, 1, 1)
            ->latestByDate()
            ->take(10)
            ->get()
            ->flatMap(function ($item) {
                $decoded = is_array($item->images) ? $item->images : json_decode($item->images, true);
                return collect($decoded)->map(fn($img) => asset('storage/' . $img));
            })
            ->shuffle()
            ->take(8);

        // Teglar
        $tags = Tag::all();

        // Statistika
        $stat = SiteStat::first();

        return view('pages.index', compact(
            'latestNews',
            'otherNews',
            'announcements',
            'announcementsWithActivity',
            'faculties',
            'departments',
            'galleryImages',
            'tags',
            'stat'
        ));
    }

    public function menuIndex(string $menu)
    {
        $locale   = app()->getLocale();
        $slugCol  = 'slug_' . $locale;

        $menuModel = Menu::where($slugCol, $menu)->firstOrFail();

        $submenus = $menuModel->submenus()->orderBy('order')->paginate(9);

        // Meta
        $metaTitle = $menuModel->{'title_' . $locale} ?? $menuModel->title_uz ?? 'Menu';
        $metaDescription = Str::limit(strip_tags($menuModel->{'title_' . $locale} ?? ''), 150);
        $metaImage = $menuModel->image
            ? asset('storage/' . $menuModel->image)
            : asset('img/og-image.webp');

        return view('pages.menu.index', [
            'menuModel'     => $menuModel,
            'submenus'      => $submenus,
            'metaTitle'     => $metaTitle,
            'metaDescription' => $metaDescription,
            'metaImage'     => $metaImage,
            'canonical'     => url()->current(),
        ]);
    }

    public function submenuIndex(string $menu, string $submenu)
    {
        $locale   = app()->getLocale();
        $slugCol  = 'slug_' . $locale;

        // Menu va Submenu’ni topish
        $menuModel = Menu::where($slugCol, $menu)->firstOrFail();

        $submenuModel = Submenu::where($slugCol, $submenu)
            ->where('menu_id', $menuModel->id)
            ->firstOrFail();

        // Shu submenu ostidagi barcha Multimenu’lar
        $multimenus = Multimenu::query()
            ->where('menu_id', $menuModel->id)
            ->where('submenu_id', $submenuModel->id)
            ->orderBy('order')
            ->orderBy('id')
            ->paginate(9);

        // Meta
        $metaTitle = $submenuModel->{'title_' . $locale} ?? $submenuModel->title_uz ?? 'Submenu';
        $metaDescription = Str::limit(strip_tags($submenuModel->{'title_' . $locale} ?? ''), 150);
        $metaImage = $submenuModel->image
            ? asset('storage/' . $submenuModel->image)
            : asset('img/og-image.webp');

        return view('pages.submenu.index', [
            'menuModel'     => $menuModel,
            'submenuModel'  => $submenuModel,
            'multimenus'    => $multimenus,
            'metaTitle'     => $metaTitle,
            'metaDescription' => $metaDescription,
            'metaImage'     => $metaImage,
            'canonical'     => url()->current(),
        ]);
    }

    public function pagesShow($menu, $submenu = null, $multimenu = null)
    {
        $locale = app()->getLocale();

        // Model topish
        $menuModel = $this->findMenuOrFail($menu, $locale);
        $submenuModel = $this->findSubmenuOrFail($submenu, $menuModel->id, $locale);
        $multimenuModel = $this->findMultimenuOrFail($multimenu, $submenuModel->id, $locale);

        // Sahifa query
        $pagesQuery = Page::where([
            'menu_id' => $menuModel->id,
            'submenu_id' => $submenuModel->id,
            'multimenu_id' => $multimenuModel->id,
        ]);

        // Umuman sahifa yo'q bo‘lsa
        if (!$pagesQuery->exists()) {
            return view('pages.updating', compact('menuModel', 'submenuModel', 'multimenuModel'));
        }

        $pageType = $pagesQuery->value('page_type');

        // Ko‘p sahifali turlar
        if (in_array($pageType, ['blog', 'faculty', 'department'])) {
            $pages = $pagesQuery->latest('date')->paginate(9);

            return view(match ($pageType) {
                'blog' => 'pages.blog.index',
                'faculty' => 'pages.faculty.index',
                'department' => 'pages.department.index',
            }, [
                'pages' => $pages,
                'menuModel' => $menuModel,
                'submenuModel' => $submenuModel,
                'multimenuModel' => $multimenuModel,
                'metaTitle' => $multimenuModel->{'title_' . $locale},
                'metaImage' => $multimenuModel->image
                    ? asset('storage/' . $multimenuModel->image)
                    : asset('img/og-image.webp'),
            ]);
        }

        // Bitta sahifa
        $page = $pagesQuery->with(['files', 'staffMembers'])->firstOrFail();

        $this->incrementViewOnce($page);

        return view(match ($pageType) {
            'center' => 'pages.center',
            'section' => 'pages.section',
            default => 'pages.default',
        }, [
            'page' => $page,
            'menuModel' => $menuModel,
            'submenuModel' => $submenuModel,
            'multimenuModel' => $multimenuModel,
            'locale' => $locale,
            'metaTitle' => $page->{'title_' . $locale},
            'metaDescription' => Str::limit(strip_tags($page->{'content_' . $locale}), 150),
            'metaImage' => $page->image
                ? asset('storage/' . $page->image)
                : asset('img/og-image.webp'),
        ]);
    }

    public function pageDetail(string $menu, string $submenu, string $multimenu, string $page)
    {
        $locale   = app()->getLocale();
        $slugCol  = 'slug_' . $locale;

        $menuModel = Menu::where($slugCol, $menu)->firstOrFail();
        $submenuModel = Submenu::where($slugCol, $submenu)
            ->where('menu_id', $menuModel->id)
            ->firstOrFail();
        $multimenuModel = Multimenu::where($slugCol, $multimenu)
            ->where('submenu_id', $submenuModel->id)
            ->firstOrFail();

        // Shu kontekstdagi page’ni topamiz:
        $pageModel = Page::where([
            'menu_id'      => $menuModel->id,
            'submenu_id'   => $submenuModel->id,
            'multimenu_id' => $multimenuModel->id,
        ])
            ->where(function ($q) use ($slugCol, $page) {
                $q->where($slugCol, $page)
                    ->orWhere('id', preg_replace('/^.*-(\d+)$/', '$1', $page)); // fallback: slug-123 → id=123
            })
            ->with(['files', 'staffMembers', 'departmentHistory'])
            ->firstOrFail();

        // View count (cookie bilan 1 marta)
        $this->incrementViewOnce($pageModel);

        // Turiga qarab view:
        $view = match ($pageModel->page_type) {
            'blog'      => 'pages.blog.show',
            'faculty'   => 'pages.faculty.show',
            'department' => 'pages.department.show',
        };


        $raw = $pageModel->images;
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

        // URL’ga aylantirib yuboramiz:
        $images = array_map(function ($p) {
            $p = ltrim($p, '/');
            if (Str::startsWith($p, ['http://', 'https://'])) return $p;
            if (Str::startsWith($p, 'storage/')) return asset($p);
            return asset('storage/' . $p);
        }, $images);

        return view($view, [
            'locale' => $locale,
            'page'           => $pageModel,
            'menuModel'      => $menuModel,
            'submenuModel'   => $submenuModel,
            'multimenuModel' => $multimenuModel,
            'images'         => $images,
            'metaTitle'      => $pageModel->{'title_' . $locale},
            'metaDescription' => Str::limit(strip_tags($pageModel->{'content_' . $locale}), 150),
            'metaImage'      => $pageModel->image ? asset('storage/' . $pageModel->image) : asset('img/og-image.webp'),
        ]);
    }

    public function staffShowWithPage(string $menu, string $submenu, string $multimenu, string $page, string $staff)
    {
        $locale  = app()->getLocale();
        $slugCol = 'slug_' . $locale;

        // Menu
        $menuModel = Menu::where($slugCol, $menu)->firstOrFail();

        $submenuModel = Submenu::where($slugCol, $submenu)
            ->where('menu_id', $menuModel->id)
            ->firstOrFail();

        $multimenuModel = Multimenu::where($slugCol, $multimenu)
            ->where('menu_id', $menuModel->id)
            ->where('submenu_id', $submenuModel->id)
            ->firstOrFail();

        // Page – slug yoki id bo‘yicha
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
            ->firstOrFail();

        // Staff
        $staffModel = StaffMember::query()
            ->where('page_id', $pageModel->id)
            ->where('id', (int) $staff)
            ->firstOrFail();

        return $this->renderStaffView($menuModel, $submenuModel, $multimenuModel, $pageModel, $staffModel, $locale);
    }

    public function staffShowSimple(string $menu, string $submenu, string $multimenu, string $staff)
    {
        $locale  = app()->getLocale();
        $slugCol = 'slug_' . $locale;

        // Menu
        $menuModel = Menu::where($slugCol, $menu)->firstOrFail();

        $submenuModel = Submenu::where($slugCol, $submenu)
            ->where('menu_id', $menuModel->id)
            ->firstOrFail();

        $multimenuModel = Multimenu::where($slugCol, $multimenu)
            ->where('menu_id', $menuModel->id)
            ->where('submenu_id', $submenuModel->id)
            ->firstOrFail();

        // Page – center/section da odatda multimenu ostida bitta page bo‘ladi
        $pageModel = Page::query()
            ->where('menu_id', $menuModel->id)
            ->where('submenu_id', $submenuModel->id)
            ->where('multimenu_id', $multimenuModel->id)
            ->firstOrFail();

        // Staff
        $staffModel = StaffMember::query()
            ->where('page_id', $pageModel->id)
            ->where('id', (int) $staff)
            ->firstOrFail();

        return $this->renderStaffView($menuModel, $submenuModel, $multimenuModel, $pageModel, $staffModel, $locale);
    }

    public function tagsIndex()
    {
        $tags = Tag::all();

        $metaTitle       = __('Teglar');
        $metaDescription = __('Veb-saytimizdagi barcha teglar ro‘yxati');
        $metaImage       = asset('img/og-image.webp');

        return view('pages.tags.index', compact('tags', 'metaTitle', 'metaDescription', 'metaImage'));
    }

    public function tagsShow(string $slug)
    {
        $locale  = app()->getLocale();

        // Tagni tilga mos slug bo‘yicha topamiz (fallback slug_uz)
        $tag = Tag::where('slug', $slug)
            ->firstOrFail();

        // Tagga biriktirilgan sahifalar (eng so‘nggi bo‘yicha, 9 tadan)
        $pages = Page::query()
            ->whereHas('tags', fn($q) => $q->where('tags.id', $tag->id))
            ->with(['tags'])                // kerak bo‘lsa teglari bilan
            ->latest('date')                // date maydoni bo‘yicha
            ->paginate(9);

        // SEO
        $metaTitle       = __('Teg') . ': ' . ($tag->name);
        $metaDescription = __('“:name” tegiga biriktirilgan sahifalar', ['name' => $tag->name]);
        $metaImage       = asset('img/og-image.webp');

        return view('pages.tags.show', compact('tag', 'pages', 'metaTitle', 'metaDescription', 'metaImage'));
    }

    /**
     * Yordamchi metodlar
     */
    private function findMenuOrFail($slug, $locale)
    {
        return Menu::where('slug_' . $locale, $slug)->firstOrFail();
    }

    private function findSubmenuOrFail($slug, $menuId, $locale)
    {
        return Submenu::where('slug_' . $locale, $slug)
            ->where('menu_id', $menuId)
            ->firstOrFail();
    }

    private function findMultimenuOrFail($slug, $submenuId, $locale)
    {
        return Multimenu::where('slug_' . $locale, $slug)
            ->where('submenu_id', $submenuId)
            ->firstOrFail();
    }


    private function incrementViewOnce($page)
    {
        // Session key nomini yaratamiz
        $sessionKey = 'page_viewed_' . $page->id;

        // Agar sessionda yo'q bo'lsa
        if (!Session::has($sessionKey)) {
            // Viewlarni 1 taga oshiramiz
            $page->increment('views');

            // Sessionga yozib qo'yamiz
            Session::put($sessionKey, true);
        }
    }

    protected function renderStaffView($menuModel, $submenuModel, $multimenuModel, $pageModel, $staffModel, $locale)
    {
        $metaTitle = $staffModel->{'name_' . $locale} ?? 'Xodim';
        $metaDesc  = Str::limit(strip_tags($staffModel->{'content_' . $locale} ?? ''), 150);
        $metaImage = $staffModel->image ? asset('storage/' . $staffModel->image) : asset('img/og-image.webp');

        return view('pages.staff.show', [
            'menuModel'      => $menuModel,
            'submenuModel'   => $submenuModel,
            'multimenuModel' => $multimenuModel,
            'pageModel'      => $pageModel && in_array($pageModel->page_type, ['faculty', 'department'])
                ? $pageModel
                : null,
            'staff'          => $staffModel,
            'metaTitle'      => $metaTitle,
            'metaDescription' => $metaDesc,
            'metaImage'      => $metaImage,
            'canonical'      => url()->current(),
            'locale'         => $locale,
        ]);
    }
}