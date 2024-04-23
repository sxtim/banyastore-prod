<?php
namespace App\Models\Order;

use App\Models\Shop\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 *
 *
 * @property-read \App\Models\Order\Order|null $order
 * @property-read Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderProduct query()
 * @mixin \Eloquent
 */
class OrderProduct extends Pivot
{


    protected $fillable = [
        'order_id',
        'product_id',
        'price',
        'base_price',
        'discount',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

}
