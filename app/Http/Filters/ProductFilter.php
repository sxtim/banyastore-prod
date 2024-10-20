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
        foreach ($properties as $value) {
            $this->builder->whereHas('propertiesValues', function (Builder $query) use ($value) {
                $query->where(function($query) use ($value) {
                    $query->where('id', '=', $value);
                });
            });
        }
    }

}
