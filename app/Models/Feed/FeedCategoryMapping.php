<?php

namespace App\Models\Feed;

use App\Models\Shop\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedCategoryMapping extends Model
{
    protected $fillable = [
        'feed_source_id',
        'external_id',
        'external_name',
        'category_id',
        'is_excluded',
    ];

    protected $casts = ['is_excluded' => 'boolean'];

    public function source(): BelongsTo
    {
        return $this->belongsTo(FeedSource::class, 'feed_source_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
