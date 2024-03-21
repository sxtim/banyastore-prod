<?php

namespace App\Models\Shop;

use App\Http\Filters\Filterable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discount extends Model
{
    use Filterable;

    protected $table = 'discounts';

    const TYPE_RUB = 'rub';
    const TYPE_PERCENT = 'percent';

    protected $fillable = [
        'name',
        'type',
        'discount',
        'is_active'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'discount_id')->orderBy('sort');
    }
}
