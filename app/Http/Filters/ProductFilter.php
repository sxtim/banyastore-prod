<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class ProductFilter extends QueryFilter
{

    public function searchProduct($search): Builder
    {
        return $this->builder->where(function ($query) use ($search){
            $query->where('name', 'like', "%$search%");
        });
    }

    public function category($category): Builder
    {
        return $this->builder->where('category_id', $category);
    }

    public function properties($properties)
    {
        foreach ($properties as $values) {
            $this->builder->whereHas('propertiesValues', function (Builder $query) use ($values) {
                $query->where(function($q) use ($values) {
                    foreach ($values as $value) {
                        if (is_numeric($value)) {
                            $q->orWhere('id', '=', $value);
                        }
                    }
                });
            });
        }
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
                return $this->builder->orderBy('price','desc');
        }
        return $this->builder->orderBy('sort','desc');
    }
}
