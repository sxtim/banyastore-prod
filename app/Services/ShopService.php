<?php

namespace App\Services;

use App\Models\Shop\Product;

class ShopService
{
    public function getPropertiesForFilter(int $categoryId): array
    {
        $products = Product::with(['propertiesValues','propertiesValues.property'])
            ->where('category_id', '=', $categoryId)
            ->where('is_active', true)
            ->get();

        $properties = [];
        foreach ($products as $product) {
            foreach ($product->propertiesValues as $propertyValue) {
                $foundKeyProperty = array_search($propertyValue->property->id, array_column($properties, 'id'));
                if ($foundKeyProperty === false) {
                    $properties[] = [
                        'id' => $propertyValue->property->id,
                        'name' => $propertyValue->property->name,
                        'values' => [
                            [
                                'id' => $propertyValue->id,
                                'name' => $propertyValue->name
                            ]
                        ]
                    ];
                } else {
                    $foundValue = in_array($propertyValue->id, array_column($properties[$foundKeyProperty]['values'], 'id'));
                    if ($foundValue === false) {
                        $properties[$foundKeyProperty]['values'][] = [
                            'id' => $propertyValue->id,
                            'name' => $propertyValue->name
                        ];
                    }
                }
            }
        }

        return $properties;
    }
}

