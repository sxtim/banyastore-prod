<?php

namespace App\Services\Feed;

class IronSteelBrandSeriesResolver
{
    private const BRAND_ALIASES = [
        'prometall' => 'ПроМеталл',
        'pro metall' => 'ПроМеталл',
        'прометалл' => 'ПроМеталл',
        'про металл' => 'ПроМеталл',
        'craft' => 'Крафт',
        'kraft' => 'Крафт',
        'крафт' => 'Крафт',
        'ferrum' => 'Феррум',
        'феррум' => 'Феррум',
    ];

    private const SERIES_PATTERNS = [
        'Атмосфера' => '/(?<![\pL])атмосфера(?![\pL])/iu',
        'Эйфория' => '/(?<![\pL])эйфория(?![\pL])/iu',
        'Бахтинка' => '/(?<![\pL])бахтинка(?![\pL])/iu',
        'Тайга' => '/(?<![\pL])тайга(?![\pL])/iu',
        'Буредан' => '/(?<![\pL])буредан(?![\pL])/iu',
        'Бахта' => '/(?<![\pL])бахта(?![\pL])/iu',
        'Маэстро' => '/(?<![\pL])маэстро(?![\pL])/iu',
    ];

    public function resolve(
        array $params,
        string $name,
        ?string $vendor,
        string $categoryName
    ): array {
        $descriptionBrand = $params['Бренд'] ?? null;
        $brand = $this->brand($vendor, $descriptionBrand);
        if ($brand !== null) {
            $params['Бренд'] = $brand;
        } else {
            unset($params['Бренд']);
        }

        $series = $this->series($name, $categoryName);
        if ($series !== null) {
            $params['Серия'] = $series;
        }

        return $params;
    }

    private function brand(?string $vendor, ?string $descriptionBrand): ?string
    {
        $value = trim((string) ($vendor ?: $descriptionBrand));
        if ($value === '') {
            return null;
        }

        $key = mb_strtolower((string) preg_replace('/\s+/u', ' ', $value));

        return self::BRAND_ALIASES[$key] ?? $value;
    }

    private function series(string $name, string $categoryName): ?string
    {
        if (! $this->isStoveCategory($categoryName) || ! $this->isStoveProductName($name)) {
            return null;
        }

        foreach (self::SERIES_PATTERNS as $series => $pattern) {
            if (preg_match($pattern, $name)) {
                return $series;
            }
        }

        return null;
    }

    private function isStoveProductName(string $name): bool
    {
        return (bool) preg_match(
            '/^(?:печь|печи|отопительная печь|интерьерная печь|банная печь)(?:-камин)?\b/iu',
            trim($name)
        );
    }

    private function isStoveCategory(string $categoryName): bool
    {
        return $categoryName === 'Комбинированная отделка'
            || $categoryName === 'Отопительные печи'
            || str_starts_with($categoryName, 'Банные печи');
    }
}
