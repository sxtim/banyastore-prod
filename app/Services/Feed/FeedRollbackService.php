<?php

namespace App\Services\Feed;

use App\Jobs\RollbackFeedImportItem;
use App\Models\Feed\FeedImportRun;
use Illuminate\Support\Facades\Bus;

class FeedRollbackService
{
    public function __construct(
        private readonly FeedRunFinalizer $finalizer,
        private readonly FeedRunGuard $runGuard,
    ) {}

    public function start(FeedImportRun $applyRun, ?int $userId): FeedImportRun
    {
        [$run, $jobs] = $this->runGuard->exclusive(
            $applyRun->feed_source_id,
            function () use ($applyRun, $userId) {
                $applyRun->refresh();
                if (
                    $applyRun->kind !== FeedImportRun::KIND_APPLY
                    || ! in_array($applyRun->status, [
                        FeedImportRun::STATUS_COMPLETED,
                        FeedImportRun::STATUS_COMPLETED_WITH_ERRORS,
                    ], true)
                ) {
                    throw new FeedException('Откатить можно только завершённый импорт.');
                }

                $latest = FeedImportRun::query()
                    ->where('feed_source_id', $applyRun->feed_source_id)
                    ->where('kind', FeedImportRun::KIND_APPLY)
                    ->latest('id')
                    ->first();

                if (! $latest || $latest->id !== $applyRun->id) {
                    throw new FeedException('Разрешён откат только последнего импорта.');
                }

                $run = FeedImportRun::query()->create([
                    'feed_source_id' => $applyRun->feed_source_id,
                    'user_id' => $userId,
                    'parent_run_id' => $applyRun->id,
                    'kind' => FeedImportRun::KIND_ROLLBACK,
                    'status' => FeedImportRun::STATUS_RUNNING,
                    'started_at' => now(),
                ]);

                $jobs = [];
                foreach ($applyRun->items()
                    ->whereIn('status', ['success', 'warning'])
                    ->whereNotNull('before_snapshot')
                    ->orderByDesc('id')
                    ->get() as $sourceItem) {
                    $item = $run->items()->create([
                        'feed_product_link_id' => $sourceItem->feed_product_link_id,
                        'product_id' => $sourceItem->product_id,
                        'offer_id' => $sourceItem->offer_id,
                        'action' => 'rollback',
                        'status' => 'ready',
                        'feed_payload' => $sourceItem->feed_payload,
                        'diff' => ['source_item_id' => $sourceItem->id],
                        'before_snapshot' => $sourceItem->before_snapshot,
                    ]);
                    $jobs[] = new RollbackFeedImportItem($item->id);
                }

                return [$run, $jobs];
            }
        );

        if ($jobs === []) {
            $this->finalizer->finalizeIfFinished($run);
            $applyRun->update(['status' => FeedImportRun::STATUS_ROLLED_BACK]);

            return $run->fresh();
        }

        try {
            $batch = Bus::batch($jobs)
                ->name("Feed rollback {$run->id}")
                ->allowFailures()
                ->onQueue('feed-imports')
                ->dispatch();
        } catch (\Throwable $exception) {
            $message = 'Откат не поставлен в очередь: '.$exception->getMessage();
            $run->items()->where('status', 'ready')->update([
                'status' => 'error',
                'error' => $message,
            ]);
            $run->update([
                'status' => FeedImportRun::STATUS_FAILED,
                'error' => $message,
                'finished_at' => now(),
            ]);
            throw new FeedException('Не удалось поставить откат в очередь.', 0, $exception);
        }

        $run->update(['batch_id' => $batch->id]);

        return $run->fresh();
    }
}
