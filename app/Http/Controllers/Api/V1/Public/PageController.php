<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PageDetailResource;
use App\Http\Resources\Api\V1\PageListResource;
use App\Http\Traits\Api\ApiResponses;
use App\Models\Menu;
use App\Models\Multimenu;
use App\Models\Page;
use App\Models\Submenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    use ApiResponses;

    public function index(Request $request)
    {
        $query = Page::query()->with('tags:id,name,slug');

        if ($request->filled('type')) {
            $query->where('page_type', $request->input('type'));
        }

        if ($request->filled('menu_id')) {
            $query->where('menu_id', $request->integer('menu_id'));
        }

        if ($request->filled('submenu_id')) {
            $query->where('submenu_id', $request->integer('submenu_id'));
        }

        if ($request->filled('multimenu_id')) {
            $query->where('multimenu_id', $request->integer('multimenu_id'));
        }

        $sort = $request->input('sort', '-date');
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');

        $allowedSorts = ['date', 'views', 'order', 'id', 'created_at'];
        if (in_array($column, $allowedSorts)) {
            $query->orderBy($column, $direction);
        } else {
            $query->latest('date');
        }

        $perPage = min($request->integer('per_page', 15), 50);

        return $this->paginatedResponse(PageListResource::collection($query->paginate($perPage)));
    }

    public function show(int $id)
    {
        $page = Page::with($this->pageDetailRelations())->find($id);

        if (!$page) {
            return $this->notFoundResponse('Page not found');
        }

        $this->incrementViewOnce($page);

        return $this->successResponse(new PageDetailResource($page));
    }

    public function byPath(string $menu, string $sub, string $multi, Request $request)
    {
        $locale = app()->getLocale();

        $menuModel = $this->findBySlug(Menu::class, $locale, $menu);
        if (!$menuModel) return $this->notFoundResponse('Menu not found');

        $submenuModel = $this->findBySlug(Submenu::class, $locale, $sub, ['menu_id' => $menuModel->id]);
        if (!$submenuModel) return $this->notFoundResponse('Submenu not found');

        $multimenuModel = $this->findBySlug(Multimenu::class, $locale, $multi, ['submenu_id' => $submenuModel->id]);
        if (!$multimenuModel) return $this->notFoundResponse('Multimenu not found');

        $query = Page::with('tags:id,name,slug')
            ->where(function ($q) use ($menuModel, $submenuModel, $multimenuModel) {
                $q->where([
                    'menu_id' => $menuModel->id,
                    'submenu_id' => $submenuModel->id,
                    'multimenu_id' => $multimenuModel->id,
                ]);
            })
            ->orWhereHas('multimenus', fn ($q) => $q->where('multimenus.id', $multimenuModel->id));

        $perPage = min($request->integer('per_page', 15), 50);

        return $this->paginatedResponse(
            PageListResource::collection($query->latest('date')->paginate($perPage))
        );
    }

    public function detailByPath(string $menu, string $sub, string $multi, string $page)
    {
        $locale = app()->getLocale();
        $slugCol = "slug_{$locale}";

        $menuModel = $this->findBySlug(Menu::class, $locale, $menu);
        if (!$menuModel) return $this->notFoundResponse('Menu not found');

        $submenuModel = $this->findBySlug(Submenu::class, $locale, $sub, ['menu_id' => $menuModel->id]);
        if (!$submenuModel) return $this->notFoundResponse('Submenu not found');

        $multimenuModel = $this->findBySlug(Multimenu::class, $locale, $multi, ['submenu_id' => $submenuModel->id]);
        if (!$multimenuModel) return $this->notFoundResponse('Multimenu not found');

        $multimenuId = $multimenuModel->id;

        $pageModel = Page::where(function ($q) use ($menuModel, $submenuModel, $multimenuId) {
                $q->where([
                    'menu_id' => $menuModel->id,
                    'submenu_id' => $submenuModel->id,
                    'multimenu_id' => $multimenuId,
                ])->orWhereHas('multimenus', fn ($mq) => $mq->where('multimenus.id', $multimenuId));
            })
            ->where(function ($q) use ($slugCol, $page) {
                $q->where($slugCol, $page);

                $id = (int) preg_replace('/^.*-(\d+)$/', '$1', $page);
                if ($id > 0) {
                    $q->orWhere('id', $id);
                }

                if (is_numeric($page)) {
                    $q->orWhere('id', (int) $page);
                }
            })
            ->with($this->pageDetailRelations())
            ->first();

        if (!$pageModel) {
            return $this->notFoundResponse('Page not found');
        }

        $this->incrementViewOnce($pageModel);

        return $this->successResponse(new PageDetailResource($pageModel));
    }

    private function pageDetailRelations(): array
    {
        return [
            'tags:id,name,slug',
            'files:id,page_id,name,file',
            'menu',
            'submenu',
            'multimenu',
            'staffCategories' => function ($query) {
                $query->whereNull('parent_id')
                    ->with([
                        'staffMembers:id,name_uz,name_ru,name_en,position_uz,position_ru,position_en,image,staff_category_id,page_id',
                        'children' => fn ($q) => $q->with('staffMembers:id,name_uz,name_ru,name_en,position_uz,position_ru,position_en,image,staff_category_id,page_id'),
                    ]);
            },
            'departmentHistory',
        ];
    }

    private function findBySlug(string $modelClass, string $locale, string $slug, array $where = [])
    {
        return $modelClass::where(function ($q) use ($locale, $slug) {
                $q->where("slug_{$locale}", $slug)->orWhere('slug_uz', $slug);
            })
            ->where($where)
            ->first();
    }

    private function incrementViewOnce(Page $page): void
    {
        if (!config('site.view_tracking.enabled', true)) {
            return;
        }

        $cacheKey = "page_viewed_{$page->id}_" . request()->ip();
        $ttl = config('site.view_tracking.ttl', 3600);

        if (!Cache::has($cacheKey)) {
            $page->increment('views');
            Cache::put($cacheKey, true, $ttl);
        }
    }
}
