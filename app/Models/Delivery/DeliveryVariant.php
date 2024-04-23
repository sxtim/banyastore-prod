<?php
namespace App\Models\Delivery;

use Illuminate\Database\Eloquent\Model;


class DeliveryVariant extends Model
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
