<?php

namespace App\Services\Feed;

use App\Models\Feed\FeedImportRun;
use Illuminate\Support\Facades\Storage;

class FeedRunFinalizer
{
    public function finalizeIfFinished(FeedImportRun $run): void
    {
        $run->refresh();
        if ($run->status !== FeedImportRun::STATUS_RUNNING) {
            return;
        }

        if ($run->items()->whereIn('status', ['ready', 'running'])->exists()) {
            return;
        }

        $counts = $run->items()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->map(fn ($value) => (int) $value)
            ->all();

        $hasProblems = ($counts['error'] ?? 0) > 0 || ($counts['warning'] ?? 0) > 0;
        $run->update([
            'status' => $hasProblems
                ? FeedImportRun::STATUS_COMPLETED_WITH_ERRORS
                : FeedImportRun::STATUS_COMPLETED,
            'summary' => [
                'total' => array_sum($counts),
                'success' => $counts['success'] ?? 0,
                'warning' => $counts['warning'] ?? 0,
                'error' => $counts['error'] ?? 0,
                'skipped' => $counts['skipped'] ?? 0,
            ],
            'finished_at' => now(),
        ]);

        if (
            $run->kind === FeedImportRun::KIND_APPLY
            && ($counts['error'] ?? 0) === 0
        ) {
            FeedImportRun::query()
                ->where('feed_source_id', $run->feed_source_id)
                ->where('kind', FeedImportRun::KIND_APPLY)
                ->where('id', '<', $run->id)
                ->pluck('id')
                ->each(fn (int $runId) => Storage::deleteDirectory("feed-imports/rollback/{$runId}"));
        }
    }
}
