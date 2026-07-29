<?php

namespace App\Services\Feed;

use App\Models\Feed\FeedCategoryMapping;
use App\Models\Feed\FeedSource;
use App\Models\Shop\Category;

class FeedCategoryResolver
{
    public function resolve(FeedSource $source, array $offer): ?Category
    {
        $mapping = FeedCategoryMapping::query()
            ->with('category')
            ->where('feed_source_id', $source->id)
            ->where('external_id', $offer['category_id'])
            ->first();

        if (! $mapping || $mapping->is_excluded) {
            return null;
        }

        if (
            $offer['category_id'] === '303980426391'
            && mb_stripos($offer['name'], 'облицовка стены') !== false
        ) {
            return Category::query()->where('name', 'Облицовка камнем')->first();
        }

        return $mapping->category;
    }

    public function isExcluded(FeedSource $source, string $externalId): bool
    {
        return FeedCategoryMapping::query()
            ->where('feed_source_id', $source->id)
            ->where('external_id', $externalId)
            ->where('is_excluded', true)
            ->exists();
    }
}
