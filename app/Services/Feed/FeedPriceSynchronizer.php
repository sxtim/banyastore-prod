<?php

namespace App\Services\Feed;

use App\Models\Feed\FeedSource;
use App\Models\Shop\Discount;
use App\Models\Shop\Product;
use Illuminate\Support\Str;

class FeedPriceSynchronizer
{
    public function sync(
        Product $product,
        FeedSource $source,
        string $offerId,
        array $payload
    ): ?array {
        $price = (float) $payload['price'];
        $oldPrice = isset($payload['old_price']) ? (float) $payload['old_price'] : null;
        $managedDiscount = Discount::query()
            ->where('feed_source_id', $source->id)
            ->where('feed_offer_id', $offerId)
            ->first();
        $before = $managedDiscount ? $this->attributes($managedDiscount) : null;

        if ($oldPrice !== null && $oldPrice > $price) {
            $managedDiscount ??= new Discount([
                'feed_source_id' => $source->id,
                'feed_offer_id' => $offerId,
            ]);
            $managedDiscount->fill([
                'name' => Str::limit(
                    $source->name.' · '.($payload['name'] ?? $offerId),
                    255,
                    ''
                ),
                'type' => Discount::TYPE_RUB,
                'discount' => $oldPrice - $price,
                'is_active' => true,
            ]);
            $managedDiscount->save();

            $product->update([
                'price' => $oldPrice,
                'discount_id' => $managedDiscount->id,
            ]);

            return [
                'id' => $managedDiscount->id,
                'before' => $before,
            ];
        }

        if ($managedDiscount) {
            $managedDiscount->update(['is_active' => false]);
        }

        $product->update([
            'price' => $price,
            'discount_id' => null,
        ]);

        return $managedDiscount ? [
            'id' => $managedDiscount->id,
            'before' => $before,
        ] : null;
    }

    public function rollback(?array $state): void
    {
        if (! $state || empty($state['id'])) {
            return;
        }

        $discount = Discount::query()->find($state['id']);
        if (! $discount) {
            return;
        }

        if (! empty($state['before'])) {
            $discount->update($state['before']);

            return;
        }

        $discount->update(['is_active' => false]);
    }

    private function attributes(Discount $discount): array
    {
        return [
            'feed_source_id' => $discount->feed_source_id,
            'feed_offer_id' => $discount->feed_offer_id,
            'name' => $discount->name,
            'type' => $discount->type,
            'discount' => $discount->discount,
            'is_active' => $discount->is_active,
        ];
    }
}
