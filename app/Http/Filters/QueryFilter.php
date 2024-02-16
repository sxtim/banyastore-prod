<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class QueryFilter
{

    protected Request $request;

    protected Builder $builder;

    protected string $delimiter = ',';

    public function __construct(Request $request)
    {

        $this->request = $request;
    }

    public function filters()
    {
        return $this->request->query();
    }

    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach($this->filters() as $name => $value) {
            $name = Str::camel($name);
            if(method_exists($this, $name)) {
                call_user_func_array([$this, $name], array_filter([$value]));
            }
        }

        return $this->builder;
    }
}
