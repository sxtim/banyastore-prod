<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\News;
use App\Models\Shop\Product;
use Illuminate\Contracts\View\View;

class IndexController extends Controller
{
    public function index(): View
    {
        $news = News::where('start_at', '<=', now())
            ->orWhere('start_at', '=', null)
            ->where('end_at','>=', now())
            ->orWhere('end_at', '=', null)
            ->where('is_active', true)
            ->orderBy('sort', 'asc')
            ->limit(5)
            ->get();

        $products = Product::with(['discount'])
            ->where('is_popular', true)
            ->where('is_active', true)
            ->get();

        $banners = Banner::where('is_active', true)
            ->orderBy('sort', 'asc')
            ->get();

        return view('index', compact('news','products','banners'));
    }

    public function agreeText(): View
    {
        return view('agree-text');
    }
}
