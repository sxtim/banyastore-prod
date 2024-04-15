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
 * @property-read \App\Models\Basket\SessionBasket $basket
 * @property-read Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|SessionBasketProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionBasketProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionBasketProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionBasketProduct whereBasketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionBasketProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionBasketProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionBasketProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionBasketProduct whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionBasketProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SessionBasketProduct extends Model
{
    protected $table = 'session_basket_product';

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
        return $this->belongsTo(SessionBasket::class, 'basket_id');
    }
}
