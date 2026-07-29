<?php

namespace App\Services\Feed;

class FeedValueNormalizer
{
    public function display(string $value): string
    {
        $value = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = str_replace(["\u{00A0}", "\r", "\n", "\t"], ' ', $value);
        $value = preg_replace('/\s+/u', ' ', $value);
        $value = preg_replace('/(?<=\d)\s*[xх×*]\s*(?=\d)/iu', '×', (string) $value);
        $value = preg_replace('/м\s*3(?=\b|$)/iu', 'м³', (string) $value);
        $value = preg_replace('/(?<=\d)(?=(?:кг|мм|см|м³|кВт|л)\b)/iu', ' ', (string) $value);

        return trim((string) preg_replace('/\s+/u', ' ', (string) $value));
    }

    public function comparisonKey(string $value): string
    {
        $value = mb_strtolower($this->display($value));
        $value = str_replace(['–', '—', '−'], '-', $value);
        $value = preg_replace('/(?<=\d),(?=\d)/u', '.', $value);
        $value = preg_replace('/литр(?:а|ов)?/u', 'л', (string) $value);
        $value = str_replace(['×', 'х', ' ', 'м³'], ['x', 'x', '', 'м3'], (string) $value);

        return trim((string) $value);
    }
}
