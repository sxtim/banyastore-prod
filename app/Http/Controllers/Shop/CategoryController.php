<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Category;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::with(['subcategory'])->where('parent_id', null)->get();
        return view('shop.category.index', compact('categories'));
    }
}
