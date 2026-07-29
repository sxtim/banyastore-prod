<?php

namespace App\Jobs;

use App\Models\Feed\FeedImportItem;
use App\Services\Feed\FeedProductImporter;
use App\Services\Feed\FeedRunFinalizer;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ApplyFeedImportItem implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 240;

    public function __construct(public readonly int $itemId) {}

    public function handle(FeedProductImporter $importer, FeedRunFinalizer $finalizer): void
    {
        $item = FeedImportItem::query()->findOrFail($this->itemId);
        $importer->import($item);
        $finalizer->finalizeIfFinished($item->run);
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
