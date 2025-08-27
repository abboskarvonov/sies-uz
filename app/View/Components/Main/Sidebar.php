<?php

namespace App\View\Components\Main;

use App\Models\Menu;
use App\Models\Tag;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Sidebar extends Component
{
    public $menus;
    public $tags;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // Menyular (faol bo'lganlari)
        $this->menus = Menu::where('status', 'active')
            ->orderBy('order')
            ->get();

        // Teglar
        $this->tags = Tag::all();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.main.sidebar');
    }
}
