<?php

namespace App\Services\Feed;

use App\Models\Feed\FeedImportItem;
use App\Models\Feed\FeedImportRun;
use App\Models\Feed\FeedProductLink;
use App\Models\Feed\FeedSource;
use App\Models\Shop\Product;
use Illuminate\Support\Facades\Storage;

class FeedPreviewService
{
    public function __construct(
        private readonly IronSteelFeedClient $client,
        private readonly IronSteelFeedParser $parser,
        private readonly FeedCategoryResolver $categoryResolver,
        private readonly FeedCandidateMatcher $candidateMatcher,
        private readonly FeedRunGuard $runGuard,
    ) {}

    public function create(FeedSource $source, ?int $userId): FeedImportRun
    {
        return $this->process($this->start($source, $userId));
    }

    public function start(FeedSource $source, ?int $userId): FeedImportRun
    {
        return $this->runGuard->exclusive(
            $source->id,
            fn () => FeedImportRun::query()->create([
                'feed_source_id' => $source->id,
                'user_id' => $userId,
                'kind' => FeedImportRun::KIND_PREVIEW,
                'status' => FeedImportRun::STATUS_PREPARING,
                'started_at' => now(),
            ])
        );
    }

    public function process(FeedImportRun $run): FeedImportRun
    {
        try {
            $run->update([
                'status' => FeedImportRun::STATUS_RUNNING,
                'error' => null,
                'finished_at' => null,
            ]);
            $source = $run->source()->firstOrFail();
            $xml = $this->client->fetch($source->url);
            $parsed = $this->parser->parse($xml);
            $hash = hash('sha256', $xml);
            $path = "feed-imports/{$source->slug}/snapshots/{$run->id}-{$hash}.xml";
            Storage::put($path, $xml);

            $run->update([
                'snapshot_path' => $path,
                'snapshot_hash' => $hash,
                'feed_generated_at' => $parsed['generated_at'],
            ]);

            $this->buildItems($run, $source, $parsed['offers']);
            $run->update([
                'status' => FeedImportRun::STATUS_READY,
                'summary' => $this->summary($run),
                'finished_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            $run->update([
                'status' => FeedImportRun::STATUS_FAILED,
                'error' => $exception->getMessage(),
                'finished_at' => now(),
            ]);

            throw $exception;
        }

        return $run->fresh('items.product.category');
    }

    public function refreshSummary(FeedImportRun $run): void
    {
        $run->update(['summary' => $this->summary($run)]);
    }

    private function buildItems(FeedImportRun $run, FeedSource $source, array $offers): void
    {
        $seenOfferIds = [];

        foreach ($offers as $offer) {
            $seenOfferIds[] = $offer['offer_id'];
            $excluded = $this->categoryResolver->isExcluded($source, $offer['category_id']);
            $link = FeedProductLink::query()->firstOrCreate(
                [
                    'feed_source_id' => $source->id,
                    'offer_id' => $offer['offer_id'],
                ],
                [
                    'vendor_code' => $offer['vendor_code'],
                    'decision' => $excluded
                        ? FeedProductLink::DECISION_EXCLUDE
                        : FeedProductLink::DECISION_PENDING,
                ]
            );

            if ($offer['vendor_code'] && $link->vendor_code !== $offer['vendor_code']) {
                $link->update(['vendor_code' => $offer['vendor_code']]);
            }
            $this->syncLinkMetadata($link, $offer);

            $product = $link->product_id ? Product::query()
                ->with(['category', 'propertiesValues.property', 'additionalImages'])
                ->find($link->product_id) : null;

            [$action, $status, $error] = $this->resolveAction($link, $product, $offer, $source);

            FeedImportItem::query()->create([
                'feed_import_run_id' => $run->id,
                'feed_product_link_id' => $link->id,
                'product_id' => $product?->id,
                'offer_id' => $offer['offer_id'],
                'action' => $action,
                'status' => $status,
                'feed_payload' => $offer,
                'diff' => $this->diff($source, $offer, $product, $action),
                'error' => $error,
            ]);
        }

        FeedProductLink::query()
            ->where('feed_source_id', $source->id)
            ->whereNotNull('product_id')
            ->whereNotIn('offer_id', $seenOfferIds)
            ->each(function (FeedProductLink $link) use ($run) {
                FeedImportItem::query()->create([
                    'feed_import_run_id' => $run->id,
                    'feed_product_link_id' => $link->id,
                    'product_id' => $link->product_id,
                    'offer_id' => $link->offer_id,
                    'action' => 'removed',
                    'status' => 'skipped',
                    'feed_payload' => [
                        'offer_id' => $link->offer_id,
                        'vendor_code' => $link->vendor_code,
                    ],
                    'diff' => ['message' => 'Товар отсутствует в текущем фиде; каталог не изменяется.'],
                ]);
            });
    }

    private function syncLinkMetadata(FeedProductLink $link, array $offer): void
    {
        $metadata = $link->metadata ?? [];
        $changed = false;
        foreach ([
            'feed_group_id' => $offer['group_id'] ?? null,
            'feed_vendor' => $offer['vendor'] ?? null,
        ] as $key => $value) {
            if ($value === null || $value === '') {
                if (array_key_exists($key, $metadata)) {
                    unset($metadata[$key]);
                    $changed = true;
                }

                continue;
            }

            if (($metadata[$key] ?? null) !== $value) {
                $metadata[$key] = $value;
                $changed = true;
            }
        }

        if ($changed) {
            $link->update(['metadata' => $metadata ?: null]);
        }
    }

    private function resolveAction(
        FeedProductLink $link,
        ?Product $product,
        array $offer,
        FeedSource $source
    ): array {
        if (
            $link->decision === FeedProductLink::DECISION_EXCLUDE
            || $this->categoryResolver->isExcluded($source, $offer['category_id'])
        ) {
            return ['excluded', 'skipped', null];
        }

        if ($link->decision === FeedProductLink::DECISION_LINK && $product) {
            return ['update', 'ready', null];
        }

        if ($link->decision === FeedProductLink::DECISION_CREATE) {
            if ($product) {
                return ['update', 'ready', null];
            }

            $exactCandidate = $this->candidateMatcher->exactCandidate($source, $offer);
            if ($exactCandidate) {
                return [
                    'pending',
                    'pending',
                    "Найден товар с таким же названием: ID {$exactCandidate['id']}. Подтвердите связь или создание.",
                ];
            }

            if (! $this->categoryResolver->resolve($source, $offer)) {
                return ['pending', 'pending', 'Для категории фида не задано соответствие.'];
            }

            return ['create', 'ready', null];
        }

        return ['pending', 'pending', null];
    }

    private function diff(FeedSource $source, array $offer, ?Product $product, string $action): array
    {
        $category = $this->categoryResolver->resolve($source, $offer);
        $base = [
            'feed_name' => $offer['name'],
            'feed_vendor' => $offer['vendor'] ?? null,
            'feed_group_id' => $offer['group_id'] ?? null,
            'feed_price' => $offer['price'],
            'feed_old_price' => $offer['old_price'] ?? null,
            'feed_category' => $offer['category_name'],
            'target_category' => $category?->name,
            'feed_properties' => $offer['params'],
            'raw_feed_properties' => $offer['raw_params'] ?? $offer['params'],
            'description_properties' => $offer['description_params'] ?? [],
            'property_conflicts' => $offer['property_conflicts'] ?? [],
            'unmapped_description_lines' => $offer['unmapped_description_lines'] ?? [],
            'packaging_lines' => $offer['packaging_lines'] ?? [],
            'feed_photo_count' => count($offer['pictures']),
        ];

        if ($action === 'pending') {
            $base['candidates'] = $this->candidateMatcher->candidates($source, $offer);
        }

        if (! $product) {
            return $base;
        }

        $feedBasePrice = $offer['old_price'] ?? $offer['price'];
        $productCurrentPrice = $product->getCurrentPrice();

        return $base + [
            'product_name' => $product->name,
            'product_price' => $product->price,
            'product_current_price' => $productCurrentPrice,
            'product_category' => $product->category?->name,
            'product_photo_count' => ($product->image ? 1 : 0) + $product->additionalImages->count(),
            'price_changed' => (float) $product->price !== (float) $feedBasePrice
                || (float) $productCurrentPrice !== (float) $offer['price'],
            'description' => $this->hasDescription($product) ? 'Сохранить наше' : 'Заполнить из фида',
        ];
    }

    private function hasDescription(Product $product): bool
    {
        return ! empty($product->description['blocks'] ?? []);
    }

    private function summary(FeedImportRun $run): array
    {
        $counts = $run->items()
            ->selectRaw('action, COUNT(*) as aggregate')
            ->groupBy('action')
            ->pluck('aggregate', 'action')
            ->map(fn ($value) => (int) $value)
            ->all();

        return [
            'total' => $run->items()->count(),
            'update' => $counts['update'] ?? 0,
            'create' => $counts['create'] ?? 0,
            'pending' => $counts['pending'] ?? 0,
            'excluded' => $counts['excluded'] ?? 0,
            'removed' => $counts['removed'] ?? 0,
            'errors' => $run->items()->where('status', 'error')->count(),
            'property_conflicts' => $run->items()
                ->whereJsonLength('diff->property_conflicts', '>', 0)
                ->count(),
        ];
    }
}
