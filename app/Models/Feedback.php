<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{

    protected $fillable = [
        'name',
        'phone',
        'mess'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

}
