<?php

namespace App\Models\Basket;

use App\Models\Shop\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property int $basket_id
 * @property int $product_id
 * @property int $quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Basket\Basket $basket
 * @property-read Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|BasketProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BasketProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BasketProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|BasketProduct whereBasketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasketProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasketProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasketProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasketProduct whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BasketProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BasketProduct extends Model
{
    protected $table = 'basket_product';

    protected $fillable = [
        'product_id',
        'quantity',
    ];

    protected $touches = ['basket'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function basket(): BelongsTo
    {
        return $this->belongsTo(Basket::class, 'basket_id');
    }
}
