<?php

namespace App\Models\Feed;

use App\Models\Shop\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedProductLink extends Model
{
    public const DECISION_LINK = 'link';

    public const DECISION_CREATE = 'create';

    public const DECISION_PENDING = 'pending';

    public const DECISION_EXCLUDE = 'exclude';

    protected $fillable = [
        'feed_source_id',
        'offer_id',
        'vendor_code',
        'product_id',
        'decision',
        'last_status',
        'last_synced_at',
        'metadata',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(FeedSource::class, 'feed_source_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
