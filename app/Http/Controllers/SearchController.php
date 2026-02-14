<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q'); // qidiruv so'zini olish

        $results = null;

        if ($query) {
            $results = Page::query()
                ->where('title_uz', 'like', "%$query%")
                ->orWhere('title_ru', 'like', "%$query%")
                ->orWhere('title_en', 'like', "%$query%")
                ->paginate(21)
                ->appends(['q' => $query]);
        }

        return view('pages.search.result', compact('results', 'query'));
    }
}
