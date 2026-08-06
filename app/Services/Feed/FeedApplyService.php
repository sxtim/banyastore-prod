<?php

namespace App\Services\Feed;

use App\Jobs\ApplyFeedImportItem;
use App\Models\Feed\FeedImportRun;
use Illuminate\Support\Facades\Bus;

class FeedApplyService
{
    public function __construct(
        private readonly FeedRunFinalizer $finalizer,
        private readonly FeedRunGuard $runGuard,
    ) {}

    public function start(FeedImportRun $preview, ?int $userId): FeedImportRun
    {
        [$run, $jobs] = $this->runGuard->exclusive(
            $preview->feed_source_id,
            function () use ($preview, $userId) {
                $preview->refresh();
                if (
                    $preview->kind !== FeedImportRun::KIND_PREVIEW
                    || $preview->status !== FeedImportRun::STATUS_READY
                ) {
                    throw new FeedException('Запустить можно только готовую предварительную проверку.');
                }

                if (FeedImportRun::query()
                    ->where('feed_source_id', $preview->feed_source_id)
                    ->where('id', '>', $preview->id)
                    ->exists()) {
                    throw new FeedException('Проверка устарела. Перед импортом проверьте фид заново.');
                }

                if ($preview->items()->where('action', 'pending')->exists()) {
                    throw new FeedException('Сначала разберите товары во вкладке «Требуют решения».');
                }

                $readyItems = $preview->items()
                    ->whereIn('action', ['update', 'create'])
                    ->where('status', 'ready')
                    ->orderBy('id')
                    ->get();
                if ($readyItems->isEmpty()) {
                    throw new FeedException('Изменений для импорта нет.');
                }

                $run = FeedImportRun::query()->create([
                    'feed_source_id' => $preview->feed_source_id,
                    'user_id' => $userId,
                    'parent_run_id' => $preview->id,
                    'kind' => FeedImportRun::KIND_APPLY,
                    'status' => FeedImportRun::STATUS_RUNNING,
                    'snapshot_path' => $preview->snapshot_path,
                    'snapshot_hash' => $preview->snapshot_hash,
                    'feed_generated_at' => $preview->feed_generated_at,
                    'started_at' => now(),
                ]);

                $jobs = [];
                foreach ($readyItems as $previewItem) {
                    $item = $run->items()->create([
                        'feed_product_link_id' => $previewItem->feed_product_link_id,
                        'product_id' => $previewItem->product_id,
                        'offer_id' => $previewItem->offer_id,
                        'action' => $previewItem->action,
                        'status' => 'ready',
                        'feed_payload' => $previewItem->feed_payload,
                        'diff' => $previewItem->diff,
                        'error' => $previewItem->error,
                    ]);

                    $jobs[] = new ApplyFeedImportItem($item->id);
                }

                return [$run, $jobs];
            }
        );

        if ($jobs === []) {
            $this->finalizer->finalizeIfFinished($run);

            return $run->fresh();
        }

        try {
            $batch = Bus::batch($jobs)
                ->name("Feed import {$run->id}")
                ->allowFailures()
                ->onQueue('feed-imports')
                ->dispatch();
        } catch (\Throwable $exception) {
            $this->failDispatch($run, $exception);
            throw new FeedException('Не удалось поставить импорт в очередь.', 0, $exception);
        }

        $run->update(['batch_id' => $batch->id]);

        return $run->fresh();
    }

    public function retryErrors(FeedImportRun $failedRun, ?int $userId): FeedImportRun
    {
        $jobs = $this->runGuard->exclusive(
            $failedRun->feed_source_id,
            function () use ($failedRun, $userId) {
                $failedRun->refresh();
                if (
                    $failedRun->kind !== FeedImportRun::KIND_APPLY
                    || $failedRun->status !== FeedImportRun::STATUS_COMPLETED_WITH_ERRORS
                ) {
                    throw new FeedException('Повторить можно только ошибки завершённого импорта.');
                }

                $failedItems = $failedRun->items()->where('status', 'error')->orderBy('id')->get();
                if ($failedItems->isEmpty()) {
                    throw new FeedException('В этом импорте нет ошибок для повторной обработки.');
                }

                $failedRun->update([
                    'user_id' => $userId,
                    'status' => FeedImportRun::STATUS_RUNNING,
                    'summary' => null,
                    'error' => null,
                    'finished_at' => null,
                ]);

                $jobs = [];
                foreach ($failedItems as $failedItem) {
                    $failedItem->update([
                        'status' => 'ready',
                        'error' => null,
                    ]);
                    $jobs[] = new ApplyFeedImportItem($failedItem->id);
                }

                return $jobs;
            }
        );

        try {
            $batch = Bus::batch($jobs)
                ->name("Feed import retry {$failedRun->id}")
                ->allowFailures()
                ->onQueue('feed-imports')
                ->dispatch();
        } catch (\Throwable $exception) {
            $this->failDispatch($failedRun, $exception);
            throw new FeedException('Не удалось поставить повторную обработку в очередь.', 0, $exception);
        }

        $failedRun->update(['batch_id' => $batch->id]);

        return $failedRun->fresh();
    }

    private function failDispatch(FeedImportRun $run, \Throwable $exception): void
    {
        $message = 'Операция не поставлена в очередь: '.$exception->getMessage();
        $run->items()->where('status', 'ready')->update([
            'status' => 'error',
            'error' => $message,
        ]);
        $run->update([
            'status' => FeedImportRun::STATUS_FAILED,
            'error' => $message,
            'finished_at' => now(),
        ]);
    }
}
