<?php

namespace App\Http\Controllers;

use App\Models\Symbol;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;


class SymbolController extends Controller
{
    public function show($symbol)
    {
        // Faqat slug_uz bilan topish, chunki URL da uzatilgan slug doim uzbekcha
        $data = Symbol::where('slug_uz', $symbol)->firstOrFail();

        // Hozirgi tilni aniqlash (uz, ru, en)
        $locale = App::getLocale();

        // Meta title
        $metaTitle = $data->{'title_' . $locale};

        $metaDescription = Str::limit(strip_tags($data->{'content_' . $locale} ?? ''), 150);

        // Rasm umumiy bo‘lsa
        $metaImage = $data->image
            ? asset('storage/' . $data->image)
            : asset('img/og-image.webp');

        return view('pages.symbols.show', compact('data', 'metaTitle', 'metaDescription', 'metaImage', 'locale'));
    }
}
