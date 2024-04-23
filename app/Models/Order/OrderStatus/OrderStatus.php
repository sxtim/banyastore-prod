<?php
namespace App\Models\Order\OrderStatus;

use Illuminate\Database\Eloquent\Model;


class OrderStatus extends Model
{

    const STATUS_CREATE_VALUE = 10;

    protected $fillable = [
        'name',
        'value_status'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
