<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PageListResource;
use App\Http\Traits\Api\ApiResponses;
use App\Models\Page;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    use ApiResponses;

    public function index(Request $request)
    {
        $query = $request->input('q', '');

        if (mb_strlen($query) < 2) {
            return $this->errorResponse(
                'VALIDATION_ERROR',
                'Search query must be at least 2 characters.',
                422
            );
        }

        $pages = Page::query()
            ->where(function ($q) use ($query) {
                $q->where('title_uz', 'LIKE', "%{$query}%")
                    ->orWhere('title_ru', 'LIKE', "%{$query}%")
                    ->orWhere('title_en', 'LIKE', "%{$query}%");
            })
            ->with('tags:id,name,slug')
            ->latest('date')
            ->paginate(min($request->integer('per_page', 15), 50));

        return $this->paginatedResponse(PageListResource::collection($pages));
    }
}
