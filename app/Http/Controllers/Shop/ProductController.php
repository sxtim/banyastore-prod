<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Category;
use App\Models\Shop\Product;
use App\Services\Seo\SeoTemplateService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function byCategory(string $slug, SeoTemplateService $seoTemplateService): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $products = Product::with(['discount'])
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->get();

        $seo = $seoTemplateService->getTemplateCategory($category);

        return view('shop.product.index', compact('products','category','seo'));
    }

    public function detail(string $slug, SeoTemplateService $seoTemplateService): View
    {
        /**
         * @var Product $product
         */
        $product = Product::with(['category','discount','propertiesValues'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $seo = $seoTemplateService->getTemplateProduct($product);

        return view('shop.product.detail', compact('product', 'seo'));
    }

    public function search(Request $request): View
    {
        $products = Product::with(['discount'])
            ->where('name', 'like', '%'.$request->input('query').'%')
            ->where('is_active', true)
            ->get();
        return view('shop.product.search', compact('products'));
    }
}
