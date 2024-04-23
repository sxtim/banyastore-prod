<?php
namespace App\Models\Delivery;

use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Delivery extends Model
{
    protected $fillable = [
        'order_id',
        'city_name',
        'street',
        'house',
        'address'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
