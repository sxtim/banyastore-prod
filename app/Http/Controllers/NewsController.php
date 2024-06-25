<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Shop\Category;
use App\Models\Shop\Product;
use Illuminate\Contracts\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        $news = News::where('start_at', '<=', now())
            ->where('end_at','>=', now())
            ->where('is_active', true)
            ->get();
        return view('news.index', compact('news'));
    }

    public function detail(): View
    {
        $news = News::where('start_at', '<=', now())
            ->where('end_at','>=', now())
            ->where('is_active', true)
            ->firstOrFail();

        return view('news.detail', compact('news'));
    }
}
