<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsCode extends Model
{
    protected $table = 'sms_code';

    protected $fillable = [
        'phone',
        'code',
        'created_at'
    ];


    public $timestamps = false;

}
