<?php

namespace App\Models\Feed;

use App\Models\Shop\Property\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedPropertyMapping extends Model
{
    protected $fillable = ['feed_source_id', 'external_name', 'property_id', 'target_name'];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
