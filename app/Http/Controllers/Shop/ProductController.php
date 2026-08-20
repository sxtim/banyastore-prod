<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Filters\ProductFilter;
use App\Models\Shop\Category;
use App\Models\Shop\Product;
use App\Services\CatalogFacetService;
use App\Services\Seo\SeoTemplateService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function byCategory(
        string $slug,
        Request $request,
        SeoTemplateService $seoTemplateService,
        CatalogFacetService $facetService,
        ProductFilter $filter
    ): View|JsonResponse {
        $category = Category::where('slug', $slug)->firstOrFail();
        $filters = $filter->filters();
        $productsQuery = Product::filter($filter)
            ->with(['discount'])
            ->where('category_id', $category->id)
            ->where('is_active', true);

        if (! isset($filters['sort']) && ! isset($filters['sortDesk'])) {
            $productsQuery->orderBy('sort');
        }

        $products = $productsQuery
            ->orderBy('products.id')
            ->paginate(24)
            ->withQueryString();

        $selectedPrice = [
            'min' => $filters['price_min'] ?? null,
            'max' => $filters['price_max'] ?? null,
        ];
        $facetData = $facetService->getData(
            $category->id,
            $filters['properties'] ?? [],
            $selectedPrice
        );
        $properties = $facetData['properties'];

        if ($request->expectsJson()) {
            return response()
                ->json([
                    'html' => view('shop.product.partials.results', compact('products'))->render(),
                    'facets' => $facetData['facets'],
                    'result_count' => $facetData['result_count'],
                    'selected' => (object) ($filters['properties'] ?? []),
                    'selected_price' => $selectedPrice,
                    'price_bounds' => $facetData['price_bounds'],
                    'pagination' => [
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'total' => $products->total(),
                    ],
                ])
                ->header('X-Robots-Tag', 'noindex, nofollow');
        }

        $seo = $seoTemplateService->getTemplateCategory($category);
        $facets = $facetData['facets'];
        $resultCount = $facetData['result_count'];
        $priceBounds = $facetData['price_bounds'];
        $filterUi = [
            'featured_property_ids' => config(
                'catalog.filters.featured_property_ids.'.$category->slug,
                []
            ),
            'brand_property_id' => config(
                'catalog.filters.brand_property_id.'.$category->slug
            ),
            'always_open_property_ids' => config(
                'catalog.filters.always_open_property_ids.'.$category->slug,
                []
            ),
            'option_preview_limit' => (int) config('catalog.filters.option_preview_limit', 6),
        ];

        return view('shop.product.index', compact(
            'products',
            'category',
            'seo',
            'properties',
            'filters',
            'facets',
            'resultCount',
            'selectedPrice',
            'priceBounds',
            'filterUi'
        ));
    }

    public function detail(string $slug, SeoTemplateService $seoTemplateService): View
    {
        /**
         * @var Product $product
         */
        $product = Product::with(['category','discount','propertiesValues','relatedProducts','boughtTogether'])
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
