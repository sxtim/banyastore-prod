<?php

namespace App\Http\Controllers;

use App\Models\Shop\Category;
use App\Models\Shop\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class YmlController extends Controller
{
    public function catalog(Request $request): Response
    {
        $categories = Category::where('is_active', 1)
            ->get();

        $products = Product::query()
            ->where('is_active', '=', 1)
            ->with(['category', 'propertiesValues'])->get();

        $dateFile = date('Y-m-d H:i:s');

        return response()
            ->view('catalog-yml', compact(
                'products',
                'dateFile',
                'categories'
            ))
            ->header('Content-Type', 'text/xml');
    }
}
