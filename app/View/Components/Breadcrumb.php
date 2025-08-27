<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumb extends Component
{
    public $menu;
    public $submenu;
    public $multimenu;
    public $page;
    public $staff;
    /**
     * Create a new component instance.
     */
    public function __construct($menu, $submenu = null, $multimenu = null, $page = null, $staff = null)
    {
        $this->menu = $menu;
        $this->submenu = $submenu;
        $this->multimenu = $multimenu;
        $this->page = $page;
        $this->staff = $staff;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.breadcrumb');
    }
}
