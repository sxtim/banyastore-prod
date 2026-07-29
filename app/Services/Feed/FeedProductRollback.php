<?php

namespace App\Services\Feed;

use App\Models\Feed\FeedImportItem;
use App\Models\Feed\FeedProductLink;
use App\Models\Shop\Product;
use App\Models\Shop\ProductFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FeedProductRollback
{
    public function __construct(private readonly FeedImageManager $imageManager) {}

    public function rollback(FeedImportItem $item): void
    {
        $item->load(['link', 'run.parentRun']);
        $item->update(['status' => 'running', 'error' => null]);
        $snapshot = $item->before_snapshot;

        try {
            if (($snapshot['kind'] ?? null) === 'created') {
                $this->rollbackCreated($item, $snapshot);
            } else {
                $this->rollbackExisting($item, $snapshot);
            }

            $item->update(['status' => 'success']);
            $item->link?->update(['last_status' => 'rolled_back', 'last_synced_at' => now()]);
        } catch (\Throwable $exception) {
            $item->update(['status' => 'error', 'error' => $exception->getMessage()]);

            throw $exception;
        }
    }

    private function rollbackCreated(FeedImportItem $item, array $snapshot): void
    {
        $product = Product::query()->find($snapshot['product_id']);
        if (! $product) {
            return;
        }

        $product->update(['is_active' => false]);

        if ($item->link) {
            $metadata = $item->link->metadata ?? [];
            $metadata['rolled_back_product_id'] = $product->id;
            $item->link->update([
                'product_id' => null,
                'decision' => FeedProductLink::DECISION_CREATE,
                'metadata' => $metadata,
            ]);
        }
    }

    private function rollbackExisting(FeedImportItem $item, array $snapshot): void
    {
        $product = Product::query()->with('additionalImages')->findOrFail($snapshot['product_id']);
        $currentPaths = array_merge([$product->image], $product->additionalImages->pluck('file_path')->all());

        if (! empty($snapshot['image_backups'])) {
            $this->imageManager->restore($snapshot['image_backups']);
        }

        DB::transaction(function () use ($product, $snapshot) {
            $product = Product::query()->lockForUpdate()->findOrFail($product->id);
            $product->update([
                'price' => $snapshot['price'],
                'description' => $snapshot['description'],
                'image' => $snapshot['image'],
                'is_active' => $snapshot['is_active'],
            ]);
            $product->propertiesValues()->sync($snapshot['property_value_ids']);
            $product->additionalImages()->delete();

            foreach ($snapshot['additional_images'] as $path) {
                $product->additionalImages()->create([
                    'file_path' => $path,
                    'type' => ProductFile::TYPE_IMAGE,
                ]);
            }
        });

        $restoredPaths = array_merge([$snapshot['image']], $snapshot['additional_images']);
        Storage::delete(array_values(array_diff(array_filter($currentPaths), array_filter($restoredPaths))));
    }
}
