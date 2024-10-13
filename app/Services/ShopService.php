<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;

class ShopService
{
    public function getPropertiesForFilter(Collection $products): array
    {
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

