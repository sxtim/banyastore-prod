<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $table = 'payment_log';

    protected $fillable = [
        'order_id',
        'method',
        'request_params',
        'response'
    ];

    protected $casts = [
        'response' => 'array',
        'request_params' => 'array'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];


}
