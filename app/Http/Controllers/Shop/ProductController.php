<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Category;
use App\Models\Shop\Product;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function byCategory(string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $products = Product::with(['discount'])
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->get();
        return view('shop.product.index', compact('products','category'));
    }

    public function detail(string $slug): View
    {
        $product = Product::with(['category','discount','propertiesValues'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
        return view('shop.product.detail', compact('product'));
    }
}
