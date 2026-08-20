<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Http\Filters\ProductFilter;
use App\Models\Shop\Category;
use App\Services\CatalogFacetService;
use Illuminate\Http\JsonResponse;

class CatalogFacetController extends Controller
{
    public function __invoke(
        string $slug,
        ProductFilter $filter,
        CatalogFacetService $facetService
    ): JsonResponse {
        $category = Category::where('slug', $slug)->firstOrFail();
        $filters = $filter->filters();
        $selectedPrice = [
            'min' => $filters['price_min'] ?? null,
            'max' => $filters['price_max'] ?? null,
        ];
        $data = $facetService->getData(
            $category->id,
            $filters['properties'] ?? [],
            $selectedPrice
        );

        return response()
            ->json([
                'facets' => $data['facets'],
                'result_count' => $data['result_count'],
                'selected' => (object) ($filters['properties'] ?? []),
                'selected_price' => $selectedPrice,
                'price_bounds' => $data['price_bounds'],
            ])
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
