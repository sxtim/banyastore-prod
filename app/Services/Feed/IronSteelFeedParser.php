<?php

namespace App\Services\Feed;

use Carbon\CarbonImmutable;
use SimpleXMLElement;

class IronSteelFeedParser
{
    private const MAX_IMAGES_PER_OFFER = 20;

    public function __construct(private readonly IronSteelOfferNormalizer $normalizer) {}

    public function parse(string $xml): array
    {
        $previous = libxml_use_internal_errors(true);

        try {
            $document = simplexml_load_string(
                $xml,
                SimpleXMLElement::class,
                LIBXML_NOCDATA | LIBXML_NONET | LIBXML_NOBLANKS
            );
        } finally {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        if ($document === false || ! isset($document->shop->offers)) {
            $message = $errors ? trim($errors[0]->message) : 'неизвестная ошибка XML';
            throw new FeedException('Не удалось разобрать фид: '.$message);
        }

        $categories = [];
        foreach ($document->shop->categories->category ?? [] as $category) {
            $categories[(string) $category['id']] = trim((string) $category);
        }

        $offers = [];
        foreach ($document->shop->offers->offer as $offer) {
            $id = trim((string) $offer['id']);
            $name = trim((string) $offer->name);
            $price = trim((string) $offer->price);
            $oldPrice = trim((string) $offer->oldprice);
            $categoryId = trim((string) $offer->categoryId);

            if ($id === '' || $name === '' || $price === '' || $categoryId === '') {
                throw new FeedException('В фиде найден товар без id, названия, цены или категории.');
            }

            if (isset($offers[$id])) {
                throw new FeedException("В фиде повторяется offer id {$id}.");
            }

            $numericPrice = (float) $price;
            if (! is_numeric($price) || ! is_finite($numericPrice) || $numericPrice <= 0) {
                throw new FeedException("У товара {$id} некорректная цена.");
            }

            $numericOldPrice = null;
            if ($oldPrice !== '') {
                $numericOldPrice = (float) $oldPrice;
                if (
                    ! is_numeric($oldPrice)
                    || ! is_finite($numericOldPrice)
                    || $numericOldPrice <= $numericPrice
                ) {
                    throw new FeedException("У товара {$id} некорректная старая цена.");
                }
            }

            $params = [];
            foreach ($offer->param as $param) {
                $paramName = trim((string) $param['name']);
                $paramValue = trim((string) $param);
                if ($paramName !== '' && $paramValue !== '') {
                    $params[$paramName] = $paramValue;
                }
            }
            $description = trim((string) $offer->description);
            $normalized = $this->normalizer->normalize($params, $description);

            $pictures = [];
            foreach ($offer->picture as $picture) {
                $url = trim((string) $picture);
                if ($url !== '') {
                    $pictures[] = $url;
                }
            }
            $pictures = array_values(array_unique($pictures));
            if (count($pictures) > self::MAX_IMAGES_PER_OFFER) {
                throw new FeedException(
                    "У товара {$id} слишком много фотографий."
                );
            }

            $offers[$id] = [
                'offer_id' => $id,
                'group_id' => $this->nullableString($offer['group_id']),
                'vendor' => $this->nullableString($offer->vendor),
                'vendor_code' => $this->nullableString($offer->vendorCode),
                'name' => $name,
                'description' => $normalized['description'],
                'raw_description' => $description,
                'pictures' => $pictures,
                'url' => $this->nullableString($offer->url),
                'price' => $numericPrice,
                'old_price' => $numericOldPrice,
                'category_id' => $categoryId,
                'category_name' => $categories[$categoryId] ?? '',
                'params' => $normalized['params'],
                'raw_params' => $normalized['raw_params'],
                'description_params' => $normalized['description_params'],
                'property_conflicts' => $normalized['property_conflicts'],
                'unmapped_description_lines' => $normalized['unmapped_description_lines'],
                'packaging_lines' => $normalized['packaging_lines'],
            ];
        }

        return [
            'generated_at' => $this->parseDate((string) $document['date']),
            'categories' => $categories,
            'offers' => $offers,
        ];
    }

    private function nullableString(?SimpleXMLElement $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function parseDate(string $value): ?CarbonImmutable
    {
        if (trim($value) === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
