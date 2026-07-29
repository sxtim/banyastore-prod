<?php

namespace App\Services\Feed;

use App\Models\Feed\FeedPropertyMapping;
use App\Models\Feed\FeedSource;
use App\Models\Shop\Product;
use App\Models\Shop\Property\PropertyValue;

class FeedPropertySynchronizer
{
    public function __construct(private readonly FeedValueNormalizer $values) {}

    public function sync(Product $product, FeedSource $source, array $params): void
    {
        $mappings = FeedPropertyMapping::query()
            ->where('feed_source_id', $source->id)
            ->whereIn('external_name', array_keys($params))
            ->get()
            ->keyBy('external_name');

        $valuesByProperty = [];
        foreach ($params as $externalName => $valueName) {
            $mapping = $mappings->get($externalName);
            if (! $mapping?->property_id || trim($valueName) === '') {
                continue;
            }

            $value = $this->propertyValue($mapping->property_id, $valueName);
            if ($value->trashed()) {
                $value->restore();
            }
            $valuesByProperty[$mapping->property_id] = $value->id;
        }

        if ($valuesByProperty === []) {
            return;
        }

        $managedValueIds = PropertyValue::query()
            ->whereIn('property_id', array_keys($valuesByProperty))
            ->pluck('id');

        $product->propertiesValues()->detach($managedValueIds);
        $product->propertiesValues()->syncWithoutDetaching(array_values($valuesByProperty));
    }

    private function propertyValue(int $propertyId, string $valueName): PropertyValue
    {
        $displayValue = $this->values->display($valueName);
        $comparisonKey = $this->values->comparisonKey($displayValue);
        $existing = PropertyValue::query()
            ->withTrashed()
            ->where('property_id', $propertyId)
            ->get()
            ->first(
                fn (PropertyValue $value) => $this->values->comparisonKey($value->name)
                    === $comparisonKey
            );

        return $existing ?? PropertyValue::query()->create([
            'property_id' => $propertyId,
            'name' => $displayValue,
        ]);
    }
}
