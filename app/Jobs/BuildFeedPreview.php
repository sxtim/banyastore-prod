<?php

namespace App\Jobs;

use App\Models\Feed\FeedImportRun;
use App\Services\Feed\FeedPreviewService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class BuildFeedPreview implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 90;

    public function __construct(public readonly int $runId) {}

    public function handle(FeedPreviewService $previewService): void
    {
        $run = FeedImportRun::query()->findOrFail($this->runId);
        $previewService->process($run);
    }

    public function failed(Throwable $exception): void
    {
        $run = FeedImportRun::query()->find($this->runId);
        if (! $run || $run->status === FeedImportRun::STATUS_FAILED) {
            return;
        }

        $run->update([
            'status' => FeedImportRun::STATUS_FAILED,
            'error' => $exception->getMessage(),
            'finished_at' => now(),
        ]);
    }
}
