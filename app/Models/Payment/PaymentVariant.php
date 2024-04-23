<?php
namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;


class PaymentVariant extends Model
{


    protected $fillable = [
        'name',
        'slug'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

}
