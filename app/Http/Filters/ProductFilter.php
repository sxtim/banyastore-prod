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

}
