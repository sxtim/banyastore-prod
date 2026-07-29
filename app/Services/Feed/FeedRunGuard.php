<?php

namespace App\Services\Feed;

use App\Models\Feed\FeedImportRun;
use App\Models\Feed\FeedSource;
use Closure;
use Illuminate\Support\Facades\DB;

class FeedRunGuard
{
    public function exclusive(int $sourceId, Closure $callback, ?int $ignoreRunId = null): mixed
    {
        return DB::transaction(function () use ($sourceId, $callback, $ignoreRunId) {
            FeedSource::query()->lockForUpdate()->findOrFail($sourceId);

            $active = FeedImportRun::query()
                ->where('feed_source_id', $sourceId)
                ->whereIn('status', [
                    FeedImportRun::STATUS_PREPARING,
                    FeedImportRun::STATUS_RUNNING,
                ])
                ->when($ignoreRunId, fn ($query) => $query->where('id', '!=', $ignoreRunId))
                ->exists();

            if ($active) {
                throw new FeedException('Другая операция уже выполняется.');
            }

            return $callback();
        });
    }
}
