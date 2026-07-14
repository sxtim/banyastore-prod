<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class ProductFilter extends QueryFilter
{
    private const MAX_PROPERTY_GROUPS = 20;

    private const MAX_PROPERTY_VALUES_PER_GROUP = 40;

    public function filters()
    {
        $filters = parent::filters();

        if (! array_key_exists('properties', $filters)) {
            return $filters;
        }

        $filters['properties'] = $this->normalizePropertiesFilter($filters['properties']);

        if ($filters['properties'] === []) {
            unset($filters['properties']);
        }

        return $filters;
    }

    public function searchProduct($search): Builder
    {
        return $this->builder->where(function ($query) use ($search) {
            $query->where('name', 'like', "%$search%");
        });
    }

    public function category($category): Builder
    {
        return $this->builder->where('category_id', $category);
    }

    public function properties(array $properties): Builder
    {
        return $this->builder->whereIn('products.id', function ($query) use ($properties) {
            $query
                ->select('products_property_values.product_id')
                ->from('products_property_values')
                ->join(
                    'property_values',
                    'property_values.id',
                    '=',
                    'products_property_values.property_value_id'
                )
                ->where(function ($query) use ($properties) {
                    foreach ($properties as $propertyId => $values) {
                        $query->orWhere(function ($query) use ($propertyId, $values) {
                            $query
                                ->where('property_values.property_id', $propertyId)
                                ->whereIn('property_values.id', $values);
                        });
                    }
                })
                ->groupBy('products_property_values.product_id')
                ->havingRaw(
                    'COUNT(DISTINCT property_values.property_id) = ?',
                    [count($properties)]
                );
        });
    }

    public function sort($column): Builder
    {
        switch ($column) {
            case 'price':
                return $this->builder->orderBy('price');
            case 'action':
                return $this->builder
                    ->orderByRaw("CASE WHEN tag LIKE '%action%' THEN 1 ELSE 2 END")
                    ->orderBy('sort');
            case 'hit':
                return $this->builder
                    ->orderByRaw("CASE WHEN tag LIKE '%hit%' THEN 1 ELSE 2 END")
                    ->orderBy('sort');
            case 'new':
                return $this->builder
                    ->orderByRaw("CASE WHEN tag LIKE '%new%' THEN 1 ELSE 2 END")
                    ->orderBy('sort');
            case 'popular':
                return $this->builder->orderBy('is_popular');
        }

        return $this->builder->orderBy('sort');
    }

    public function sortDesk($column): Builder
    {
        switch ($column) {
            case 'price':
                return $this->builder->orderBy('price', 'desc');
        }

        return $this->builder->orderBy('sort', 'desc');
    }

    private function normalizePropertiesFilter($properties): array
    {
        $validator = Validator::make(
            ['properties' => $properties],
            [
                'properties' => ['array', 'max:'.self::MAX_PROPERTY_GROUPS],
                'properties.*' => ['array', 'max:'.self::MAX_PROPERTY_VALUES_PER_GROUP],
                'properties.*.*' => ['integer', 'min:1'],
            ]
        );

        $validator->after(function ($validator) use ($properties) {
            if (! is_array($properties)) {
                return;
            }

            foreach (array_keys($properties) as $propertyId) {
                if (! ctype_digit((string) $propertyId)) {
                    $validator->errors()->add('properties', 'Invalid property filter.');

                    return;
                }
            }
        });

        if ($validator->fails()) {
            abort(400, 'Invalid product filters.');
        }

        $normalized = [];

        foreach ($properties as $propertyId => $values) {
            $values = array_values(array_unique(array_map('intval', $values)));

            if ($values !== []) {
                $normalized[(int) $propertyId] = $values;
            }
        }

        return $normalized;
    }
}
