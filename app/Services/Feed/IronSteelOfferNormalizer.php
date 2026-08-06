<?php

namespace App\Services\Feed;

class IronSteelOfferNormalizer
{
    private const PARAM_ALIASES = [
        'Масса камней в закрытой каменке' => 'Масса камней в каменке',
        'Объём' => 'Объем',
        'Сталь' => 'Марка стали',
    ];

    private const DESCRIPTION_ALIASES = [
        'Масса камней в закрытой каменке' => 'Масса камней в каменке',
        'Масса камней в каменке' => 'Масса камней в каменке',
        'Масса камней в сетке' => 'Масса камней в сетке',
        'Объем закрытой каменки' => 'Объем каменки',
        'Масса печи с сеткой' => 'Масса печи',
        'Размер топки (ВхДхШ)' => 'Размер топки',
        'Размер печи (ВхДхШ)' => 'Размер товара',
        'Размер (ВхДхШ)' => 'Размер товара',
        'Тип подключения дымохода' => 'Тип подключения дымохода',
        'Толщина стенок топки' => 'Толщина топки',
        'Отапливаемый объем' => 'Отапливаемый объем',
        'Диаметр дымохода' => 'Диаметр дымохода',
        'Материал корпуса' => 'Материал корпуса',
        'Мощность печи' => 'Мощность печи',
        'Размер экрана' => 'Размер экрана',
        'Объем каменки' => 'Объем каменки',
        'Объем парной' => 'Объем парной',
        'Объем топки' => 'Объем топки',
        'Масса печи' => 'Масса печи',
        'Толщина топки' => 'Толщина топки',
        'Совместимость' => 'Совместимость',
        'Облицовка' => 'Облицовка',
        'Материал' => 'Материал корпуса',
        'Режимы' => 'Режимы',
        'Бренд' => 'Бренд',
        'Цвет' => 'Цвет',
    ];

    private const IGNORED_DESCRIPTION_ALIASES = [
        'Размер в упаковке (ВхДхШ)',
        'Масса печи в упаковке',
        'Масса в упаковке',
    ];

    public function __construct(
        private readonly FeedValueNormalizer $values,
        private readonly FeedDescriptionSanitizer $descriptionSanitizer,
    ) {}

    public function normalize(array $rawParams, string $description): array
    {
        $normalizedRawParams = [];
        $params = [];

        foreach ($rawParams as $rawName => $rawValue) {
            $value = $this->values->display((string) $rawValue);
            $normalizedRawParams[$rawName] = $value;
            $expandedParams = $this->expandParam($rawName, $value);

            foreach ($expandedParams as $name => $expandedValue) {
                if (! isset($params[$name])) {
                    $params[$name] = $expandedValue;
                }
            }
        }

        $lines = $this->descriptionSanitizer->lines($this->descriptionLines($description));
        $descriptionParams = [];
        $matchedLines = [];
        $packagingLines = [];
        $packagingLineIndexes = [];

        foreach ($lines as $index => $line) {
            if ($line === '') {
                continue;
            }

            if ($this->isPackagingDescriptionLine($line)) {
                $packagingLines[] = $this->normalizePackagingLine($line);
                $packagingLineIndexes[$index] = true;

                continue;
            }

            $match = $this->matchDescriptionField($line);
            if ($match === null) {
                continue;
            }

            [$name, $value] = $match;
            $matchedLines[$index] = $name;

            if (! isset($descriptionParams[$name])) {
                $descriptionParams[$name] = $value;
            }
        }

        foreach ($descriptionParams as $name => $descriptionValue) {
            if (! isset($params[$name])) {
                $params[$name] = $descriptionValue;
            }
        }

        $cleanLines = [];
        $unmappedLines = [];
        foreach ($lines as $index => $line) {
            $matchedName = $matchedLines[$index] ?? null;
            if (isset($packagingLineIndexes[$index])) {
                continue;
            }
            if (
                $matchedName !== null
                && isset($params[$matchedName])
            ) {
                continue;
            }

            $cleanLines[] = $line;
            if ($line !== '' && $this->looksTechnical($line) && $matchedName === null) {
                $unmappedLines[] = $line;
            }
        }

        return [
            'params' => $params,
            'raw_params' => $normalizedRawParams,
            'description_params' => $descriptionParams,
            'unmapped_description_lines' => array_values(array_unique($unmappedLines)),
            'packaging_lines' => array_values(array_unique($packagingLines)),
            'description' => $this->joinLines($cleanLines),
        ];
    }

    private function expandParam(string $rawName, string $value): array
    {
        if ($rawName === 'Цвет | Теплоизоляция') {
            $parts = preg_split('/\s*\|\s*/u', $value, 2);
            if (count($parts) === 2 && $parts[0] !== '' && $parts[1] !== '') {
                return [
                    'Цвет' => $this->values->display($parts[0]),
                    'Теплоизоляция' => $this->values->display($parts[1]),
                ];
            }
        }

        return [self::PARAM_ALIASES[$rawName] ?? $rawName => $value];
    }

    private function matchDescriptionField(string $line): ?array
    {
        foreach ($this->sortedDescriptionAliases() as $alias => $name) {
            $value = $this->valueAfterAlias($line, $alias);
            if ($value !== null) {
                return [$name, $this->values->display($value)];
            }
        }

        return null;
    }

    private function sortedDescriptionAliases(): array
    {
        $aliases = self::DESCRIPTION_ALIASES;
        uksort($aliases, fn (string $left, string $right) => mb_strlen($right) <=> mb_strlen($left));

        return $aliases;
    }

    private function matchAlias(string $line, array $aliases): ?string
    {
        usort($aliases, fn (string $left, string $right) => mb_strlen($right) <=> mb_strlen($left));
        foreach ($aliases as $alias) {
            if ($this->valueAfterAlias($line, $alias) !== null) {
                return $alias;
            }
        }

        return null;
    }

    private function isPackagingDescriptionLine(string $line): bool
    {
        if ($this->matchAlias($line, self::IGNORED_DESCRIPTION_ALIASES) !== null) {
            return true;
        }

        return (bool) preg_match(
            '/^(?:'
            .'Размер(?:ы)?(?:\s+.*)?\s+(?:в|с)\s+упаковк'
            .'|Размер(?:ы)?\s+упаковк'
            .'|Масса\b.*\bв\s+упаковк'
            .')/iu',
            $line
        );
    }

    private function normalizePackagingLine(string $line): string
    {
        $line = $this->values->display($line);
        $line = preg_replace('/(?<=упаковке)\s*\(/iu', ' (', $line);
        $line = preg_replace('/\)\s*(?=\d)/u', ') ', (string) $line);

        return trim((string) preg_replace('/\s+/u', ' ', (string) $line));
    }

    private function valueAfterAlias(string $line, string $alias): ?string
    {
        $quoted = preg_quote($alias, '/');
        if (preg_match('/^'.$quoted.'\s*[-–—:]\s*(.+)$/iu', $line, $matches)) {
            return trim($matches[1]);
        }

        if (preg_match('/^'.$quoted.'\s+((?:до\s+)?\d.+)$/iu', $line, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    private function descriptionLines(string $html): array
    {
        $text = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/<\s*br\s*\/?\s*>/iu', "\n", $text);
        $text = preg_replace('/<\s*\/\s*li\s*>/iu', "\n", (string) $text);
        $text = preg_replace('/<\s*li\b[^>]*>/iu', '', (string) $text);
        $text = preg_replace('/<\s*\/?\s*(p|ul|ol)\b[^>]*>/iu', "\n", (string) $text);
        $text = str_replace("\r", '', strip_tags((string) $text));

        return array_map(
            fn (string $line) => trim((string) preg_replace('/[ \t]+/u', ' ', $line)),
            explode("\n", $text)
        );
    }

    private function joinLines(array $lines): string
    {
        $result = [];
        $previousBlank = true;
        foreach ($lines as $line) {
            $blank = $line === '';
            if ($blank && $previousBlank) {
                continue;
            }
            $result[] = $line;
            $previousBlank = $blank;
        }

        while ($result !== [] && end($result) === '') {
            array_pop($result);
        }

        return implode("\n", $result);
    }

    private function looksTechnical(string $line): bool
    {
        return (bool) preg_match(
            '/^(Объем|Размер|Материал|Каменка|Диаметр|Масса|Толщина|Режим|Гарантия|'
            .'Мощность|Отапливаемый|Тип подключения|Максимальный|Рекомендуемая|'
            .'Особенности|Колосник|Бренд)\b/iu',
            $line
        );
    }
}
