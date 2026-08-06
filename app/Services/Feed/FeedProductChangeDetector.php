<?php

namespace App\Services\Feed;

use App\Models\Feed\FeedPropertyMapping;
use App\Models\Feed\FeedSource;
use App\Models\Shop\Product;
use Illuminate\Support\Collection;

class FeedProductChangeDetector
{
    private array $mappings = [];

    public function __construct(
        private readonly FeedValueNormalizer $values,
        private readonly FeedDescriptionSanitizer $descriptionSanitizer,
    ) {}

    public function detect(
        FeedSource $source,
        array $offer,
        Product $product,
        ?array $previousPayload,
        bool $wasSynced
    ): array {
        $changes = [];
        $feedPrice = (float) $offer['price'];
        $feedBasePrice = isset($offer['old_price']) && (float) $offer['old_price'] > $feedPrice
            ? (float) $offer['old_price']
            : $feedPrice;
        $currentPrice = (float) $product->getCurrentPrice();

        if (
            ! $this->sameNumber((float) $product->price, $feedBasePrice)
            || ! $this->sameNumber($currentPrice, $feedPrice)
        ) {
            $changes['price'] = [
                'label' => 'Цена',
                'from' => $currentPrice,
                'to' => $feedPrice,
                'base_from' => (float) $product->price,
                'base_to' => $feedBasePrice,
            ];
        }

        $propertyChanges = $this->propertyChanges($source, $offer['params'] ?? [], $product);
        if ($propertyChanges !== []) {
            $changes['properties'] = $propertyChanges;
        }

        $feedPictures = array_values(array_filter($offer['pictures'] ?? []));
        $previousPictures = $previousPayload === null
            ? null
            : array_values(array_filter($previousPayload['pictures'] ?? []));
        $currentPhotoCount = ($product->image ? 1 : 0) + $product->additionalImages->count();
        if (
            $currentPhotoCount !== count($feedPictures)
            || ($previousPictures !== null && $previousPictures !== $feedPictures)
            || (! $wasSynced && $previousPictures === null && $feedPictures !== [])
        ) {
            $changes['photos'] = [
                'label' => 'Фотографии',
                'from' => $currentPhotoCount,
                'to' => count($feedPictures),
            ];
        }

        $description = $product->description;
        $cleanDescription = $this->descriptionSanitizer->editorData($description);
        if ($cleanDescription !== $description) {
            $changes['description'] = [
                'label' => 'Описание',
                'from' => 'есть технические строки',
                'to' => 'очистить технические строки',
            ];
        } elseif (! $this->hasDescription($product) && trim(strip_tags((string) ($offer['description'] ?? ''))) !== '') {
            $changes['description'] = [
                'label' => 'Описание',
                'from' => 'не заполнено',
                'to' => 'заполнить из фида',
            ];
        }

        return $changes;
    }

    private function propertyChanges(FeedSource $source, array $params, Product $product): array
    {
        $mappings = $this->propertyMappings($source);
        $desired = [];

        foreach ($params as $externalName => $value) {
            $mapping = $mappings->get($externalName);
            if (! $mapping?->property_id || trim((string) $value) === '') {
                continue;
            }

            $desired[$mapping->property_id] = [
                'label' => $mapping->target_name ?: $mapping->property?->name ?: $externalName,
                'value' => $this->values->display((string) $value),
            ];
        }

        $changes = [];
        foreach ($desired as $propertyId => $target) {
            $currentValues = $product->propertiesValues
                ->where('property_id', $propertyId)
                ->pluck('name')
                ->filter()
                ->values();
            $currentKeys = $currentValues
                ->map(fn (string $value) => $this->values->comparisonKey($value))
                ->all();
            $targetKey = $this->values->comparisonKey($target['value']);

            if (count($currentKeys) === 1 && $currentKeys[0] === $targetKey) {
                continue;
            }

            $changes[] = [
                'label' => $target['label'],
                'from' => $currentValues->isEmpty() ? 'не задано' : $currentValues->implode(', '),
                'to' => $target['value'],
            ];
        }

        return $changes;
    }

    private function propertyMappings(FeedSource $source): Collection
    {
        return $this->mappings[$source->id] ??= FeedPropertyMapping::query()
            ->with('property')
            ->where('feed_source_id', $source->id)
            ->get()
            ->keyBy('external_name');
    }

    private function hasDescription(Product $product): bool
    {
        return ! empty($product->description['blocks'] ?? []);
    }

    private function sameNumber(float $left, float $right): bool
    {
        return abs($left - $right) < 0.01;
    }
}
