<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Jobs\BuildFeedPreview;
use App\Models\Feed\FeedImportItem;
use App\Models\Feed\FeedImportRun;
use App\Models\Feed\FeedProductLink;
use App\Models\Feed\FeedSource;
use App\Models\Shop\Product;
use App\Services\Feed\FeedApplyService;
use App\Services\Feed\FeedCandidateMatcher;
use App\Services\Feed\FeedException;
use App\Services\Feed\FeedPreviewService;
use App\Services\Feed\FeedRollbackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FeedImportController extends Controller
{
    public function index(Request $request): View
    {
        $source = $this->source();
        $tab = $request->string('tab')->value() ?: 'run';
        $latestPreview = $source->runs()
            ->where('kind', FeedImportRun::KIND_PREVIEW)
            ->where('status', FeedImportRun::STATUS_READY)
            ->latest('id')
            ->first();
        $latestPreviewAttempt = $source->runs()
            ->where('kind', FeedImportRun::KIND_PREVIEW)
            ->latest('id')
            ->first();
        $latestSuccessfulImport = $source->runs()
            ->where('kind', FeedImportRun::KIND_APPLY)
            ->where('status', FeedImportRun::STATUS_COMPLETED)
            ->latest('id')
            ->first();
        $activeRun = $source->runs()
            ->whereIn('status', [FeedImportRun::STATUS_PREPARING, FeedImportRun::STATUS_RUNNING])
            ->latest('id')
            ->first();
        $completedImportForLatestPreview = $latestPreview
            ? $source->runs()
                ->where('kind', FeedImportRun::KIND_APPLY)
                ->where('parent_run_id', $latestPreview->id)
                ->whereIn('status', [
                    FeedImportRun::STATUS_COMPLETED,
                    FeedImportRun::STATUS_COMPLETED_WITH_ERRORS,
                ])
                ->latest('id')
                ->first()
            : null;
        $itemAction = $request->string('item_action')->value();
        $items = $latestPreview && ! $completedImportForLatestPreview
            ? $latestPreview->items()
                ->with(['product.category', 'link'])
                ->when(
                    $itemAction !== '',
                    fn ($query) => $query->where('action', $itemAction)
                )
                ->when(
                    $itemAction === '',
                    fn ($query) => $query->whereIn('action', ['update', 'create'])
                )
                ->orderBy('id')
                ->paginate(30, ['*'], 'items_page')
                ->withQueryString()
            : null;
        $pendingItems = $latestPreview && ! $completedImportForLatestPreview
            ? $latestPreview->items()
                ->with(['product.category', 'link'])
                ->where('action', 'pending')
                ->orderBy('id')
                ->get()
            : collect();
        $runs = $source->runs()->latest('id')->limit(30)->get();
        $rollbackableRun = $source->runs()
            ->where('kind', FeedImportRun::KIND_APPLY)
            ->latest('id')
            ->first();
        if (! in_array($rollbackableRun?->status, [
            FeedImportRun::STATUS_COMPLETED,
            FeedImportRun::STATUS_COMPLETED_WITH_ERRORS,
        ], true)) {
            $rollbackableRun = null;
        }
        $canApplyPreview = $latestPreview
            && ! $source->runs()->where('id', '>', $latestPreview->id)->exists();

        return view('backend.feed-import.index', compact(
            'source',
            'tab',
            'latestPreview',
            'latestPreviewAttempt',
            'latestSuccessfulImport',
            'completedImportForLatestPreview',
            'activeRun',
            'items',
            'pendingItems',
            'runs',
            'rollbackableRun',
            'canApplyPreview',
            'itemAction'
        ));
    }

    public function preview(
        Request $request,
        FeedPreviewService $previewService
    ): RedirectResponse {
        try {
            $run = $previewService->start($this->source(), $request->user()?->id);
            try {
                BuildFeedPreview::dispatch($run->id);
            } catch (\Throwable $exception) {
                $run->update([
                    'status' => FeedImportRun::STATUS_FAILED,
                    'error' => 'Проверка не поставлена в очередь: '.$exception->getMessage(),
                    'finished_at' => now(),
                ]);
                throw $exception;
            }

            return redirect()
                ->route('backend.feed-import.index')
                ->with('success', "Проверка #{$run->id} запущена.");
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function apply(
        Request $request,
        FeedImportRun $run,
        FeedApplyService $applyService
    ): RedirectResponse {
        try {
            $applyRun = $applyService->start($run, $request->user()?->id);

            return redirect()
                ->route('backend.feed-import.index')
                ->with('success', "Импорт #{$applyRun->id} запущен.");
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function rollback(
        Request $request,
        FeedImportRun $run,
        FeedRollbackService $rollbackService
    ): RedirectResponse {
        try {
            $rollbackRun = $rollbackService->start($run, $request->user()?->id);

            return redirect()
                ->route('backend.feed-import.index', ['tab' => 'history'])
                ->with('success', "Откат #{$rollbackRun->id} запущен.");
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function retryErrors(
        Request $request,
        FeedImportRun $run,
        FeedApplyService $applyService
    ): RedirectResponse {
        try {
            $retryRun = $applyService->retryErrors($run, $request->user()?->id);

            return redirect()
                ->route('backend.feed-import.index', ['tab' => 'history'])
                ->with('success', "Повторная обработка #{$retryRun->id} запущена.");
        } catch (\Throwable $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function decide(
        Request $request,
        FeedImportItem $item,
        FeedPreviewService $previewService,
        FeedCandidateMatcher $candidateMatcher
    ): RedirectResponse {
        $data = $request->validate([
            'decision' => ['required', Rule::in([
                FeedProductLink::DECISION_LINK,
                FeedProductLink::DECISION_CREATE,
                FeedProductLink::DECISION_PENDING,
            ])],
            'product_id' => [
                Rule::requiredIf($request->input('decision') === FeedProductLink::DECISION_LINK),
                'nullable',
                'integer',
                'exists:products,id',
            ],
        ]);

        if (
            $item->run->kind !== FeedImportRun::KIND_PREVIEW
            || $item->run->status !== FeedImportRun::STATUS_READY
            || FeedImportRun::query()
                ->where('feed_source_id', $item->run->feed_source_id)
                ->where('id', '>', $item->run->id)
                ->exists()
        ) {
            return back()->with('error', 'Эта проверка уже устарела. Проверьте фид заново.');
        }

        $productId = $data['decision'] === FeedProductLink::DECISION_LINK
            ? (int) $data['product_id']
            : null;

        if (
            $productId
            && FeedProductLink::query()
                ->where('feed_source_id', $item->run->feed_source_id)
                ->where('product_id', $productId)
                ->where('id', '!=', $item->feed_product_link_id)
                ->exists()
        ) {
            return back()->with('error', "Товар ID {$productId} уже связан с другим offer id.");
        }

        $action = match ($data['decision']) {
            FeedProductLink::DECISION_LINK => 'update',
            FeedProductLink::DECISION_CREATE => 'create',
            default => 'pending',
        };
        $diff = $item->diff ?? [];
        if ($action === 'pending') {
            $diff['candidates'] = $candidateMatcher->candidates(
                $item->run->source,
                $item->feed_payload
            );
        }

        $item->link->update([
            'decision' => $data['decision'],
            'product_id' => $productId,
        ]);
        $item->update([
            'product_id' => $productId,
            'action' => $action,
            'status' => $action === 'pending' ? 'pending' : 'ready',
            'diff' => $diff,
            'error' => null,
        ]);
        $previewService->refreshSummary($item->run);

        if ($action === 'pending') {
            return redirect()
                ->route('backend.feed-import.index', ['tab' => 'review'])
                ->with('success', "Выберите новую связь для offer id {$item->offer_id}.");
        }

        return back()->with('success', "Решение для offer id {$item->offer_id} сохранено.");
    }

    public function searchProducts(Request $request): JsonResponse
    {
        $data = $request->validate(['q' => ['required', 'string', 'min:2', 'max:100']]);
        $products = Product::query()
            ->with('category')
            ->where('name', 'like', '%'.$data['q'].'%')
            ->orderBy('name')
            ->limit(15)
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'category' => $product->category?->name,
                'image_url' => $product->image_url,
            ]);

        return response()->json($products);
    }

    public function status(FeedImportRun $run): JsonResponse
    {
        $run->refresh();
        $batch = $run->batch_id ? Bus::findBatch($run->batch_id) : null;
        $terminalStatuses = [
            FeedImportRun::STATUS_READY,
            FeedImportRun::STATUS_COMPLETED,
            FeedImportRun::STATUS_COMPLETED_WITH_ERRORS,
            FeedImportRun::STATUS_FAILED,
            FeedImportRun::STATUS_ROLLED_BACK,
        ];
        $finished = in_array($run->status, $terminalStatuses, true);
        $total = $batch?->totalJobs ?? $run->items()->count();
        $processed = $batch?->processedJobs() ?? $run->items()
            ->whereNotIn('status', ['ready', 'running'])
            ->count();
        $progress = $batch?->progress()
            ?? ($finished ? 100 : ($total > 0 ? (int) floor($processed / $total * 100) : 0));

        return response()->json([
            'id' => $run->id,
            'kind' => $run->kind,
            'kind_label' => $run->kind_label,
            'status' => $run->status,
            'status_label' => $run->status_label,
            'summary' => $run->summary,
            'error' => $run->error,
            'progress' => $progress,
            'processed' => $processed,
            'total' => $total,
            'finished' => $finished,
        ]);
    }

    public function report(FeedImportRun $run)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=feed-import-{$run->id}.csv",
        ];

        return Response::stream(function () use ($run) {
            $file = fopen('php://output', 'wb');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, [
                'offer_id',
                'Действие',
                'Статус',
                'ID товара',
                'Название фида',
                'Ошибка',
                'Не сопоставлено из описания',
                'Упаковка',
            ]);

            $run->items()->orderBy('id')->chunk(200, function ($items) use ($file) {
                foreach ($items as $item) {
                    $diff = $item->diff ?? [];
                    fputcsv($file, array_map([$this, 'safeCsvValue'], [
                        $item->offer_id,
                        $item->action,
                        $item->status,
                        $item->product_id,
                        $item->feed_payload['name'] ?? '',
                        $item->error,
                        implode('; ', $diff['unmapped_description_lines'] ?? []),
                        implode('; ', $diff['packaging_lines'] ?? []),
                    ]));
                }
            });
            fclose($file);
        }, 200, $headers);
    }

    private function source(): FeedSource
    {
        $slug = config('feed_import.iron_steel.slug');
        $source = FeedSource::query()->where('slug', $slug)->first();

        if (! $source) {
            throw new FeedException(
                'Источник ProMetall не настроен. Выполните php artisan feed:iron-steel:setup.'
            );
        }

        return $source;
    }

    private function safeCsvValue(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        return preg_match('/^\s*[=+\-@]/u', $value) ? "'".$value : $value;
    }
}
