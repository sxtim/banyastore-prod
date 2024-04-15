<?php

namespace App\Models\Basket;

use App\Http\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 *
 * @property int $id
 * @property string $session_uid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Basket\SessionBasketProduct> $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder|SessionBasket filter(\App\Http\Filters\QueryFilter $filter)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionBasket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionBasket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionBasket query()
 * @method static \Illuminate\Database\Eloquent\Builder|SessionBasket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionBasket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionBasket whereSessionUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SessionBasket whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SessionBasket extends Model
{
    use Filterable, HasFactory;

    const SESSION_UID_NAME = 'sessionUidCart';

    protected $table = 'session_baskets';

    protected $fillable = [
        'id',
        'session_uid',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(SessionBasketProduct::class, 'basket_id')->with('product');
    }
}
