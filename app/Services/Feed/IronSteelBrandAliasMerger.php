<?php

namespace App\Services\Feed;

use App\Models\Shop\Property\Property;
use App\Models\Shop\Property\PropertyValue;
use Illuminate\Support\Facades\DB;

class IronSteelBrandAliasMerger
{
    private const ALIASES = [
        'ПроМеталл' => ['Prometall'],
        'Крафт' => ['Craft'],
        'Феррум' => ['Ferrum'],
    ];

    public function merge(): void
    {
        $brand = Property::query()
            ->where('name', 'Бренд')
            ->first();

        if (! $brand) {
            return;
        }

        foreach (self::ALIASES as $canonicalName => $aliases) {
            $this->mergeValues($brand, $canonicalName, $aliases);
        }
    }

    private function mergeValues(Property $property, string $canonicalName, array $aliases): void
    {
        $values = PropertyValue::query()
            ->withTrashed()
            ->where('property_id', $property->id)
            ->whereIn('name', array_merge([$canonicalName], $aliases))
            ->lockForUpdate()
            ->get();

        $canonical = $values
            ->where('name', $canonicalName)
            ->sortBy(fn (PropertyValue $value) => $value->trashed() ? 1 : 0)
            ->first();

        if (! $canonical) {
            $canonical = PropertyValue::query()->create([
                'property_id' => $property->id,
                'name' => $canonicalName,
            ]);
        } elseif ($canonical->trashed()) {
            $canonical->restore();
        }

        $obsoleteIds = $values
            ->reject(fn (PropertyValue $value) => $value->is($canonical))
            ->pluck('id')
            ->all();

        if ($obsoleteIds === []) {
            return;
        }

        $productIds = DB::table('products_property_values')
            ->whereIn('property_value_id', $obsoleteIds)
            ->pluck('product_id')
            ->unique()
            ->values();

        $alreadyAssigned = DB::table('products_property_values')
            ->where('property_value_id', $canonical->id)
            ->whereIn('product_id', $productIds)
            ->pluck('product_id')
            ->all();

        $assignedLookup = array_fill_keys($alreadyAssigned, true);
        $rows = $productIds
            ->reject(fn (int $productId) => isset($assignedLookup[$productId]))
            ->map(fn (int $productId) => [
                'product_id' => $productId,
                'property_value_id' => $canonical->id,
            ])
            ->all();

        if ($rows !== []) {
            DB::table('products_property_values')->insert($rows);
        }

        DB::table('products_property_values')
            ->whereIn('property_value_id', $obsoleteIds)
            ->delete();

        PropertyValue::query()
            ->whereIn('id', $obsoleteIds)
            ->delete();
    }
}
