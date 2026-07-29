<?php

namespace App\Models\Feed;

use App\Models\Shop\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedImportItem extends Model
{
    protected $fillable = [
        'feed_import_run_id',
        'feed_product_link_id',
        'product_id',
        'offer_id',
        'action',
        'status',
        'feed_payload',
        'diff',
        'before_snapshot',
        'error',
    ];

    protected $casts = [
        'feed_payload' => 'array',
        'diff' => 'array',
    ];

    public function run(): BelongsTo
    {
        return $this->belongsTo(FeedImportRun::class, 'feed_import_run_id');
    }

    public function link(): BelongsTo
    {
        return $this->belongsTo(FeedProductLink::class, 'feed_product_link_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getBeforeSnapshotAttribute(?string $value): ?array
    {
        return $value === null ? null : json_decode($value, true);
    }

    public function setBeforeSnapshotAttribute(?array $value): void
    {
        $this->attributes['before_snapshot'] = $value === null
            ? null
            : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
