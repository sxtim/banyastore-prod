<?php

namespace App\Services\Feed;

use App\Models\Feed\FeedImportItem;
use App\Models\Feed\FeedProductLink;
use App\Models\Shop\Product;
use App\Models\Shop\ProductFile;
use Illuminate\Support\Facades\DB;

class FeedProductImporter
{
    public function __construct(
        private readonly FeedCategoryResolver $categoryResolver,
        private readonly FeedDescriptionFactory $descriptionFactory,
        private readonly FeedPropertySynchronizer $propertySynchronizer,
        private readonly FeedImageManager $imageManager,
        private readonly ProductSnapshotService $snapshotService,
        private readonly FeedPriceSynchronizer $priceSynchronizer,
    ) {}

    public function import(FeedImportItem $item): void
    {
        $item->load(['run.source', 'link', 'product.additionalImages', 'product.propertiesValues']);
        $item->update(['status' => 'running', 'error' => null]);

        try {
            if ($item->action === 'update') {
                $this->updateExisting($item);
            } elseif ($item->action === 'create') {
                $this->createNew($item);
            } else {
                $item->update(['status' => 'skipped']);
            }
        } catch (\Throwable $exception) {
            $item->update([
                'status' => 'error',
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    private function updateExisting(FeedImportItem $item): void
    {
        $product = $item->product;
        if (! $product) {
            throw new FeedException('Связанный товар сайта не найден.');
        }

        $payload = $item->feed_payload;
        $snapshot = $this->snapshotService->capture($product);
        $photoPaths = null;
        $photoWarning = null;
        $preparedFiles = [];

        try {
            $prepared = $this->imageManager->prepareAll(
                $payload['pictures'],
                $item->run->id,
                $item->offer_id
            );
            $preparedFiles = $prepared['files'];
            $oldPaths = array_merge([$snapshot['image']], $snapshot['additional_images']);
            $snapshot['image_backups'] = $this->imageManager->archiveCurrent(
                $oldPaths,
                $item->run->id,
                $item->offer_id
            );
            $photoPaths = $this->imageManager->movePrepared(
                $prepared['files'],
                $item->run->source->slug,
                $item->offer_id,
                $item->run->id
            );
        } catch (\Throwable $exception) {
            $this->imageManager->discardPrepared($preparedFiles);
            $photoWarning = 'Фотографии не заменены: '.$exception->getMessage();
        }

        try {
            DB::transaction(function () use ($item, $product, $payload, $snapshot, $photoPaths) {
                $product = Product::query()->lockForUpdate()->findOrFail($product->id);
                $changes = [];

                if (empty($product->description['blocks'] ?? [])) {
                    $description = $this->descriptionFactory->make(
                        $payload['description'],
                        $payload['packaging_lines'] ?? []
                    );
                    if ($description) {
                        $changes['description'] = $description;
                    }
                }

                if ($photoPaths !== null) {
                    $changes['image'] = $photoPaths[0];
                    $product->additionalImages()->delete();
                    foreach (array_slice($photoPaths, 1) as $path) {
                        $product->additionalImages()->create([
                            'file_path' => $path,
                            'type' => ProductFile::TYPE_IMAGE,
                        ]);
                    }
                }

                if ($changes !== []) {
                    $product->update($changes);
                }
                $feedDiscount = $this->priceSynchronizer->sync(
                    $product,
                    $item->run->source,
                    $item->offer_id,
                    $payload
                );
                $this->propertySynchronizer->sync($product, $item->run->source, $payload['params']);
                $snapshot['feed_discount'] = $feedDiscount;

                $item->update([
                    'product_id' => $product->id,
                    'before_snapshot' => $snapshot,
                ]);
            });
        } catch (\Throwable $exception) {
            if ($photoPaths !== null) {
                $this->imageManager->deletePaths($photoPaths);
            }
            throw $exception;
        }

        if ($photoPaths !== null) {
            $this->imageManager->deleteOriginals(
                array_merge([$snapshot['image']], $snapshot['additional_images'])
            );
        }

        $status = $photoWarning ? 'warning' : 'success';
        $item->update(['status' => $status, 'error' => $photoWarning]);
        $item->link->update([
            'product_id' => $product->id,
            'decision' => FeedProductLink::DECISION_LINK,
            'last_status' => $status,
            'last_synced_at' => now(),
        ]);
    }

    private function createNew(FeedImportItem $item): void
    {
        $payload = $item->feed_payload;
        $category = $this->categoryResolver->resolve($item->run->source, $payload);
        if (! $category) {
            throw new FeedException('Не определена категория нового товара.');
        }

        $prepared = $this->imageManager->prepareForNew(
            $payload['pictures'],
            $item->run->id,
            $item->offer_id
        );
        $photoPaths = $this->imageManager->movePrepared(
            $prepared['files'],
            $item->run->source->slug,
            $item->offer_id,
            $item->run->id
        );
        $rolledBackProduct = $this->rolledBackProduct($item);
        $oldPhotoPaths = $rolledBackProduct
            ? array_merge(
                [$rolledBackProduct->image],
                $rolledBackProduct->additionalImages->pluck('file_path')->all()
            )
            : [];

        try {
            $product = DB::transaction(function () use (
                $item,
                $payload,
                $category,
                $photoPaths,
                $rolledBackProduct
            ) {
                $description = $this->descriptionFactory->make(
                    $payload['description'],
                    $payload['packaging_lines'] ?? []
                );
                $attributes = [
                    'name' => $payload['name'],
                    'category_id' => $category->id,
                    'price' => $payload['old_price'] ?? $payload['price'],
                    'image' => $photoPaths[0],
                    'description' => $description ?? ['blocks' => []],
                    'preview_text' => null,
                    'sort' => 0,
                    'is_active' => true,
                    'is_popular' => false,
                    'tag' => null,
                ];

                if ($rolledBackProduct) {
                    $product = Product::query()->lockForUpdate()->findOrFail($rolledBackProduct->id);
                    $product->additionalImages()->delete();
                    $product->propertiesValues()->detach();
                    $product->update($attributes);
                } else {
                    $product = Product::query()->create($attributes);
                }

                foreach (array_slice($photoPaths, 1) as $path) {
                    $product->additionalImages()->create([
                        'file_path' => $path,
                        'type' => ProductFile::TYPE_IMAGE,
                    ]);
                }

                $feedDiscount = $this->priceSynchronizer->sync(
                    $product,
                    $item->run->source,
                    $item->offer_id,
                    $payload
                );
                $this->propertySynchronizer->sync($product, $item->run->source, $payload['params']);

                $item->update([
                    'product_id' => $product->id,
                    'before_snapshot' => $this->snapshotService->created($product, $feedDiscount),
                ]);

                return $product;
            });
        } catch (\Throwable $exception) {
            $this->imageManager->deletePaths($photoPaths);
            throw $exception;
        }

        $this->imageManager->deletePaths(array_diff(array_filter($oldPhotoPaths), $photoPaths));
        $warning = $prepared['warnings'] ? implode("\n", $prepared['warnings']) : null;
        $status = $warning ? 'warning' : 'success';
        $item->update(['status' => $status, 'error' => $warning]);
        $metadata = $item->link->metadata ?? [];
        unset($metadata['rolled_back_product_id']);
        $metadata['created_by_feed'] = true;
        $item->link->update([
            'product_id' => $product->id,
            'decision' => FeedProductLink::DECISION_LINK,
            'last_status' => $status,
            'last_synced_at' => now(),
            'metadata' => $metadata,
        ]);
    }

    private function rolledBackProduct(FeedImportItem $item): ?Product
    {
        $metadata = $item->link?->metadata ?? [];
        $productId = $metadata['rolled_back_product_id'] ?? null;
        if (! $productId) {
            return null;
        }

        return Product::query()
            ->with('additionalImages')
            ->whereKey($productId)
            ->where('is_active', false)
            ->first();
    }
}
