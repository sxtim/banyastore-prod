<?php
namespace App\Models\Order;

use App\Models\Delivery\Delivery;
use App\Models\Delivery\DeliveryVariant;
use App\Models\Order\OrderStatus\OrderStatus;
use App\Models\Payment\PaymentVariant;
use App\Models\Shop\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;


/**
 *
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $status_id
 * @property string|null $payment_at
 * @property string $price
 * @property string $name
 * @property string $phone
 * @property string $mail
 * @property int $payment_variant_id
 * @property int $delivery_variant_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Delivery> $deliveries
 * @property-read int|null $deliveries_count
 * @property-read DeliveryVariant $deliveryVariant
 * @property-read PaymentVariant $paymentVariant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Product> $products
 * @property-read int|null $products_count
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeliveryVariantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereMail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentVariantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
 * @mixin \Eloquent
 */
class Order extends Model
{


    protected $fillable = [
        'name',
        'phone',
        'mail',
        'payment_at',
        'user_id',
        'payment_variant_id',
        'delivery_variant_id',
        'price',
        'status_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];


    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->using(OrderProduct::class)
            ->withPivot('id', 'product_id', 'price', 'base_price', 'discount');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function deliveryVariant(): BelongsTo
    {
        return $this->belongsTo(DeliveryVariant::class, 'delivery_variant_id');
    }

    public function paymentVariant(): BelongsTo
    {
        return $this->belongsTo(PaymentVariant::class, 'payment_variant_id');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'order_id');
    }

    public function lastDelivery(): ?Delivery
    {
        return $this->deliveries->last();
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }

    public function listProducts(): Collection
    {
        $result = collect();
        foreach ($this->products as $product) {
            $element = $result->where('product_id', '=', $product->id)->first();
            if ($element) {
                $result->transform(function ($item) use ($product){
                    if ($item['product_id'] == $product->id) {
                        $item['quantity']++;
                        $item['totalPrice'] = round($item['price'] * $item['quantity'],2);
                    }
                    return $item;
                });
            } else {
                $result->push([
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'totalPrice' => $product->pivot->price,
                    'price' => $product->pivot->price,
                    'base_price' => $product->pivot->base_price,
                    'discount' => $product->pivot->discount,
                    'quantity' => 1
                ]);
            }
        }
        return $result;
    }
}
