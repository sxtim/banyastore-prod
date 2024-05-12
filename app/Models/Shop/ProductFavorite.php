<?php

namespace App\Models\Shop;

use App\Http\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;


class ProductFavorite extends Model
{
    protected $table = "product_favorite";

    use Filterable;

    protected $fillable = [
        'product_id',
        'user_id',
    ];
}
