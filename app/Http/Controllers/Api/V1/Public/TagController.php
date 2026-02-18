<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PageListResource;
use App\Http\Resources\Api\V1\TagResource;
use App\Http\Traits\Api\ApiResponses;
use App\Models\Page;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;

class TagController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $ttl = config('site.cache.ttl.tags', 7200);

        $tags = config('site.cache.enabled')
            ? Cache::remember('api:tags', $ttl, fn () => Tag::withCount('pages')->orderBy('order')->get())
            : Tag::withCount('pages')->orderBy('order')->get();

        return $this->successResponse(TagResource::collection($tags));
    }

    public function show(string $slug)
    {
        $tag = Tag::where('slug', $slug)->first();

        if (!$tag) {
            return $this->notFoundResponse('Tag not found');
        }

        $pages = Page::whereHas('tags', fn ($q) => $q->where('tags.id', $tag->id))
            ->with('tags:id,name,slug')
            ->latest('date')
            ->paginate(min(request()->integer('per_page', 15), 50));

        return $this->paginatedResponse(PageListResource::collection($pages));
    }
}
