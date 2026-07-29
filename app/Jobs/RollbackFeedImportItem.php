<?php

namespace App\Jobs;

use App\Models\Feed\FeedImportItem;
use App\Models\Feed\FeedImportRun;
use App\Services\Feed\FeedProductRollback;
use App\Services\Feed\FeedRunFinalizer;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class RollbackFeedImportItem implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 180;

    public function __construct(public readonly int $itemId) {}

    public function handle(FeedProductRollback $rollback, FeedRunFinalizer $finalizer): void
    {
        $item = FeedImportItem::query()->findOrFail($this->itemId);
        $rollback->rollback($item);
        $finalizer->finalizeIfFinished($item->run);

        $item->run->refresh();
        if ($item->run->status === FeedImportRun::STATUS_COMPLETED) {
            $item->run->parentRun?->update(['status' => FeedImportRun::STATUS_ROLLED_BACK]);
        }
    }

    public function failed(Throwable $exception): void
    {
        $item = FeedImportItem::query()->find($this->itemId);
        if (! $item) {
            return;
        }

        $item->update(['status' => 'error', 'error' => $exception->getMessage()]);
        app(FeedRunFinalizer::class)->finalizeIfFinished($item->run);
    }
}
