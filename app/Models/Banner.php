<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $fillable = [
        'name',
        'sort',
        'link',
        'image',
        'is_active'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];


    public function getUrlImage(): ?string
    {
        return Storage::url($this->image);
    }

}
