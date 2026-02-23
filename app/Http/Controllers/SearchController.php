<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim($request->input('q', ''));

        $results = null;

        if (mb_strlen($query) >= 2) {
            $results = Page::query()
                ->where(function ($q) use ($query) {
                    $q->where('title_uz', 'like', "%{$query}%")
                        ->orWhere('title_ru', 'like', "%{$query}%")
                        ->orWhere('title_en', 'like', "%{$query}%");
                })
                ->select(['id', 'title_uz', 'title_ru', 'title_en', 'slug_uz', 'slug_ru', 'slug_en', 'image', 'date', 'menu_id', 'submenu_id', 'multimenu_id'])
                ->latest('date')
                ->paginate(21)
                ->appends(['q' => $query]);
        }

        return view('pages.search.result', compact('results', 'query'));
    }
}
