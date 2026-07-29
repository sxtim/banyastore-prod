<?php

namespace App\Services\Feed;

use App\Models\Feed\FeedCategoryMapping;
use App\Models\Feed\FeedPropertyMapping;
use App\Models\Feed\FeedSource;
use App\Models\Shop\Category;
use App\Models\Shop\Property\Property;
use Illuminate\Support\Facades\DB;

class IronSteelSetupService
{
    public function sync(): FeedSource
    {
        $config = config('feed_import.iron_steel');

        return DB::transaction(function () use ($config) {
            $source = FeedSource::query()->updateOrCreate(
                ['slug' => $config['slug']],
                [
                    'name' => $config['name'],
                    'url' => $config['url'],
                    'is_active' => true,
                ]
            );

            foreach ($config['categories'] as $externalId => $mapping) {
                $categoryId = null;
                if (! ($mapping['excluded'] ?? false)) {
                    $category = Category::query()->where('name', $mapping['target'])->first();
                    if (! $category && ($mapping['create_if_missing'] ?? false)) {
                        $category = Category::query()->create([
                            'name' => $mapping['target'],
                            'is_active' => true,
                            'sort' => ((int) Category::query()->max('sort')) + 1,
                        ]);
                    }
                    if (! $category) {
                        throw new FeedException("Не найдена категория сайта «{$mapping['target']}».");
                    }
                    $categoryId = $category->id;
                }

                FeedCategoryMapping::query()->updateOrCreate(
                    [
                        'feed_source_id' => $source->id,
                        'external_id' => $externalId,
                    ],
                    [
                        'external_name' => $mapping['name'],
                        'category_id' => $categoryId,
                        'is_excluded' => (bool) ($mapping['excluded'] ?? false),
                    ]
                );
            }

            foreach ($config['properties'] as $externalName => $targetName) {
                $property = Property::query()->withTrashed()->where('name', $targetName)->first();
                if ($property?->trashed()) {
                    $property->restore();
                }
                $property ??= Property::query()->create([
                    'name' => $targetName,
                    'is_required' => false,
                ]);

                FeedPropertyMapping::query()->updateOrCreate(
                    [
                        'feed_source_id' => $source->id,
                        'external_name' => $externalName,
                    ],
                    [
                        'property_id' => $property->id,
                        'target_name' => $targetName,
                    ]
                );
            }

            return $source->fresh();
        });
    }
}
