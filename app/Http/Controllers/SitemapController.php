<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\News;
use App\Models\Shop\Category;
use App\Models\Shop\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $news = News::where('start_at', '<=', now())
            ->orWhere('start_at', '=', null)
            ->where('end_at','>=', now())
            ->orWhere('end_at', '=', null)
            ->where('is_active', true)
            ->orderBy('sort', 'asc')
            ->limit(5)
            ->get();

        $products = Product::where('is_active', true)->get();

        $categories = Category::where('is_active', true)->get();

        $actions = Action::where(function($query) {
            $query->where('start_at', '<=', now())
                ->orWhere('start_at', '=', null);
        })
            ->where(function($query) {
                $query->where('end_at','>=', now())
                    ->orWhere('end_at', '=', null);
            })
            ->where('is_active', true)
            ->orderBy('sort', 'asc')
            ->get();

        $date = date('Y-m-d');

        return
            response()
                ->view(
                    'sitemap',
                    compact('news','products','actions','categories','date')
                )
                ->header('Content-Type', 'text/xml');
    }
}
