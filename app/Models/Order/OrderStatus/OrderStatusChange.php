<?php
namespace App\Models\Order\OrderStatus;

use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class OrderStatusChange extends Model
{

    protected $table = 'order_status_change';

    protected $fillable = [
        'order_id',
        'old_status_id',
        'new_status_id',
        'changed_user_id'
    ];

    protected $hidden = [
        'changed_at',
        'created_at',
        'updated_at'
    ];

    public function oldStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'old_status_id');
    }

    public function newStatus(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'new_status_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
