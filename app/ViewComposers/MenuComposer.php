<?php

namespace App\ViewComposers;

use App\Models\Shop\Category;
use Illuminate\View\View;

class MenuComposer
{
    public function compose(View $view)
    {
        $categories = Category::query()
            ->orderBy('sort')
            ->where('is_active','=',true)
            ->where('parent_id','=',null)
            ->get();

        $view->with('menu', $categories);
    }
}
