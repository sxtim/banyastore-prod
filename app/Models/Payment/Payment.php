<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'order_id',
        'payment_at',
        'foreign_id',
        'order_number_payment_system',
        'payment_url',
        'amount'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];


}
