<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\StorePageRequest;
use App\Http\Requests\Api\V1\Admin\UpdatePageRequest;
use App\Http\Resources\Api\V1\Admin\PageResource;
use App\Http\Traits\Api\ApiResponses;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PageController extends Controller
{
    use ApiResponses;

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Page::class);

        $user = $request->user();
        $query = Page::with(['tags:id,name,slug', 'createdBy:id,name', 'updatedBy:id,name']);

        // Filter by user access
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin') && !$user->can('view_all_pages')) {
            $assignedPageIds = $user->assignedPages()->pluck('pages.id');

            if ($user->can('view_blog_pages')) {
                $query->where(function ($q) use ($assignedPageIds) {
                    $q->where('page_type', 'blog')
                        ->orWhereIn('id', $assignedPageIds);
                });
            } else {
                $query->whereIn('id', $assignedPageIds);
            }
        }

        if ($request->filled('type')) {
            $query->where('page_type', $request->input('type'));
        }

        if ($request->filled('menu_id')) {
            $query->where('menu_id', $request->integer('menu_id'));
        }

        $perPage = min($request->integer('per_page', 15), 50);

        return $this->paginatedResponse(
            PageResource::collection($query->latest('date')->paginate($perPage))
        );
    }

    public function show(int $page)
    {
        $pageModel = Page::with([
            'tags:id,name,slug',
            'createdBy:id,name',
            'updatedBy:id,name',
            'files:id,page_id,name,file',
        ])->find($page);

        if (!$pageModel) {
            return $this->notFoundResponse('Page not found');
        }

        Gate::authorize('view', $pageModel);

        return $this->successResponse(new PageResource($pageModel));
    }

    public function store(StorePageRequest $request)
    {
        $data = $request->validated();
        $tags = $data['tags'] ?? [];
        unset($data['tags']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('pages', 'public');
        }

        $page = Page::create($data);

        if (!empty($tags)) {
            $page->tags()->sync($tags);
        }

        $page->load(['tags:id,name,slug', 'createdBy:id,name', 'updatedBy:id,name']);

        return $this->successResponse(new PageResource($page), 201);
    }

    public function update(UpdatePageRequest $request, int $page)
    {
        $pageModel = Page::find($page);

        if (!$pageModel) {
            return $this->notFoundResponse('Page not found');
        }

        Gate::authorize('update', $pageModel);

        $data = $request->validated();
        $tags = $data['tags'] ?? null;
        unset($data['tags']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('pages', 'public');
        }

        $pageModel->update($data);

        if ($tags !== null) {
            $pageModel->tags()->sync($tags);
        }

        $pageModel->load(['tags:id,name,slug', 'createdBy:id,name', 'updatedBy:id,name']);

        return $this->successResponse(new PageResource($pageModel));
    }

    public function destroy(int $page)
    {
        $pageModel = Page::find($page);

        if (!$pageModel) {
            return $this->notFoundResponse('Page not found');
        }

        Gate::authorize('delete', $pageModel);

        $pageModel->delete();

        return $this->successResponse(['message' => 'Page deleted successfully.']);
    }
}
