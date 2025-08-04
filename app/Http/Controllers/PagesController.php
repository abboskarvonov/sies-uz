<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Tag;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index()
    {
        // Eng oxirgi yangilik
        $latestNews = Page::where('page_type', 'blog')
            ->where('menu_id', 1)
            ->where('submenu_id', 1)
            ->where('multimenu_id', 1)
            ->latest('created_at')
            ->first();

        // Keyingi 3 ta yangilik
        $otherNews = Page::where('page_type', 'blog')
            ->where('menu_id', 1)
            ->where('submenu_id', 1)
            ->where('multimenu_id', 1)
            ->where('id', '!=', optional($latestNews)->id) // eng oxirgi yangilikni chiqarib tashlash
            ->latest('date')
            ->take(3)
            ->get();

        // E'lonlar (eng oxirgi 3 tasi)
        $announcements = Page::where('page_type', 'blog')
            ->where('menu_id', 1)
            ->where('submenu_id', 1)
            ->where('multimenu_id', 2)
            ->latest('date')
            ->take(3)
            ->get();

        $announcementsWithActivity = Page::where('page_type', 'blog')
            ->where('menu_id', 1)
            ->where('submenu_id', 1)
            ->where('multimenu_id', 2)
            ->where('activity', true)
            ->latest('date')   // eng oxirgi bo‘yicha
            ->take(4)          // 4 ta
            ->get();

        $faculties = Page::where('page_type', 'faculty')
            ->orderBy('created_at', 'asc') // eng birinchi kiritilganlari
            ->take(4)
            ->get();

        $departments = Page::where('page_type', 'department')
            ->orderBy('created_at', 'asc') // eng birinchi kiritilganlari
            ->take(6)
            ->get();

        // Oxirgi newslarni olib kelamiz (ko‘proq olib, keyin rasmlardan kesamiz)
        $news = Page::where('page_type', 'blog')
            ->where('menu_id', 1)
            ->where('submenu_id', 1)
            ->where('multimenu_id', 1)
            ->latest('date')
            ->take(10) // 10 ta so‘nggi news (etarlilik uchun)
            ->get();

        // Rasmlarni yig‘amiz
        $images = collect();

        foreach ($news as $item) {
            if (!empty($item->images)) {
                $decoded = is_array($item->images) ? $item->images : json_decode($item->images, true);
                if (is_array($decoded)) {
                    foreach ($decoded as $img) {
                        $images->push(asset('storage/' . $img));
                    }
                }
            }
        }

        // Faqat 8 tasini olamiz
        $galleryImages = $images->shuffle()->take(8);

        // Teglar
        $tags = Tag::all();

        return view('pages.index', compact('latestNews', 'otherNews', 'announcements', 'announcementsWithActivity', 'faculties', 'departments', 'galleryImages', 'tags'));
    }
}
