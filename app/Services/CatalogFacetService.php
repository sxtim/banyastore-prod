<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class CatalogFacetService
{
    public function getData(int $categoryId, array $selected = [], array $selectedPrice = []): array
    {
        $selected = $this->normalizeSelected($selected);
        $selectedPrice = $this->normalizeSelectedPrice($selectedPrice);
        $rows = $this->getCategoryRows($categoryId);
        $products = [];
        $productPrices = [];
        $properties = [];
        $propertyIndexes = [];
        $valueIndexes = [];

        foreach ($rows as $row) {
            $productId = (int) $row->product_id;
            $products[$productId] ??= [];
            $productPrices[$productId] = (int) $row->product_price;

            if ($row->property_id === null || $row->value_id === null) {
                continue;
            }

            $propertyId = (int) $row->property_id;
            $valueId = (int) $row->value_id;
            $products[$productId][$propertyId][$valueId] = true;

            if (! array_key_exists($propertyId, $propertyIndexes)) {
                $propertyIndexes[$propertyId] = count($properties);
                $valueIndexes[$propertyId] = [];
                $properties[] = [
                    'id' => $propertyId,
                    'name' => $row->property_name,
                    'values' => [],
                ];
            }

            if (! array_key_exists($valueId, $valueIndexes[$propertyId])) {
                $propertyIndex = $propertyIndexes[$propertyId];
                $valueIndexes[$propertyId][$valueId] = true;
                $properties[$propertyIndex]['values'][] = [
                    'id' => $valueId,
                    'name' => $row->value_name,
                ];
            }
        }

        $facets = [];
        $resultCount = 0;

        foreach ($products as $productId => $productValues) {
            if ($this->matches(
                $productValues,
                $selected,
                null,
                $productPrices[$productId],
                $selectedPrice
            )) {
                $resultCount++;
            }
        }

        foreach ($properties as $property) {
            $propertyId = $property['id'];
            $facets[$propertyId] = [];

            foreach ($products as $productId => $productValues) {
                if (! $this->matches(
                    $productValues,
                    $selected,
                    $propertyId,
                    $productPrices[$productId],
                    $selectedPrice
                )) {
                    continue;
                }

                foreach (array_keys($productValues[$propertyId] ?? []) as $valueId) {
                    $facets[$propertyId][$valueId] = ($facets[$propertyId][$valueId] ?? 0) + 1;
                }
            }
        }

        return [
            'properties' => $properties,
            'facets' => $facets,
            'result_count' => $resultCount,
            'price_bounds' => [
                'min' => $productPrices === [] ? 0 : min($productPrices),
                'max' => $productPrices === [] ? 0 : max($productPrices),
            ],
        ];
    }

    private function getCategoryRows(int $categoryId)
    {
        return DB::table('products')
            ->leftJoin('products_property_values as product_values', 'product_values.product_id', '=', 'products.id')
            ->leftJoin('property_values', function ($join) {
                $join
                    ->on('property_values.id', '=', 'product_values.property_value_id')
                    ->whereNull('property_values.deleted_at');
            })
            ->leftJoin('properties', function ($join) {
                $join
                    ->on('properties.id', '=', 'property_values.property_id')
                    ->whereNull('properties.deleted_at');
            })
            ->where('products.category_id', $categoryId)
            ->where('products.is_active', true)
            ->orderBy('products.sort')
            ->orderBy('products.id')
            ->orderBy('product_values.property_value_id')
            ->get([
                'products.id as product_id',
                'products.price as product_price',
                'properties.id as property_id',
                'properties.name as property_name',
                'property_values.id as value_id',
                'property_values.name as value_name',
            ]);
    }

    private function normalizeSelected(array $selected): array
    {
        $normalized = [];

        foreach ($selected as $propertyId => $values) {
            if (! is_array($values)) {
                continue;
            }

            $normalized[(int) $propertyId] = array_values(array_unique(array_map('intval', $values)));
        }

        return array_filter($normalized);
    }

    private function normalizeSelectedPrice(array $selectedPrice): array
    {
        return array_filter([
            'min' => isset($selectedPrice['min']) ? (int) $selectedPrice['min'] : null,
            'max' => isset($selectedPrice['max']) ? (int) $selectedPrice['max'] : null,
        ], static fn ($value) => $value !== null);
    }

    private function matches(
        array $productValues,
        array $selected,
        ?int $excludedPropertyId = null,
        ?int $productPrice = null,
        array $selectedPrice = []
    ): bool {
        if (isset($selectedPrice['min']) && $productPrice < $selectedPrice['min']) {
            return false;
        }

        if (isset($selectedPrice['max']) && $productPrice > $selectedPrice['max']) {
            return false;
        }

        foreach ($selected as $propertyId => $selectedValueIds) {
            if ($propertyId === $excludedPropertyId) {
                continue;
            }

            $productValueIds = array_keys($productValues[$propertyId] ?? []);

            if (array_intersect($selectedValueIds, $productValueIds) === []) {
                return false;
            }
        }

        return true;
    }
}
