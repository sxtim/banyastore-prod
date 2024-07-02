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
        $news = News::where(function($query) {
                $query->where('start_at', '<=', now())
                    ->orWhere('start_at', '=', null);
            })
            ->where(function($query) {
                $query->where('end_at','>=', now())
                    ->orWhere('end_at', '=', null);
            })
            ->where('is_active', true)
            ->orderBy('id', 'desc')
            ->get();
        return view('news.index', compact('news'));
    }

    public function detail(): View
    {
        $news = News::where(function($query) {
            $query->where('start_at', '<=', now())
                ->orWhere('start_at', '=', null);
        })
            ->where(function($query) {
                $query->where('end_at','>=', now())
                    ->orWhere('end_at', '=', null);
            })
            ->where('is_active', true)
            ->firstOrFail();

        return view('news.detail', compact('news'));
    }
}
