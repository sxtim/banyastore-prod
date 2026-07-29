<?php

namespace App\Models\Feed;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedSource extends Model
{
    protected $fillable = ['name', 'slug', 'url', 'is_active', 'settings'];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function productLinks(): HasMany
    {
        return $this->hasMany(FeedProductLink::class);
    }

    public function runs(): HasMany
    {
        return $this->hasMany(FeedImportRun::class);
    }
}
