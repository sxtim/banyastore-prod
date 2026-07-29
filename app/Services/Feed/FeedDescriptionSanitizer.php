<?php

namespace App\Services\Feed;

class FeedDescriptionSanitizer
{
    public function lines(array $lines): array
    {
        $result = [];

        foreach ($lines as $line) {
            $sanitized = $this->line((string) $line);
            if ($sanitized !== null) {
                $result[] = $sanitized;
            }
        }

        return $result;
    }

    public function editorData(?array $description): ?array
    {
        if ($description === null || ! isset($description['blocks'])) {
            return $description;
        }

        $blocks = [];
        foreach ($description['blocks'] as $block) {
            $text = $block['data']['text'] ?? null;
            if (! is_string($text)) {
                $blocks[] = $block;

                continue;
            }

            $parts = preg_split('/(?:<br\s*\/?>\s*|\R)/iu', $text);
            $cleanParts = [];
            foreach ($parts ?: [] as $part) {
                $plain = trim(strip_tags(html_entity_decode($part, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                $sanitized = $this->line($plain);
                if ($sanitized === null || $sanitized === '') {
                    continue;
                }

                $cleanParts[] = $sanitized === $plain ? trim($part) : e($sanitized);
            }

            if ($cleanParts === []) {
                continue;
            }

            $block['data']['text'] = implode("<br>\n", $cleanParts);
            $blocks[] = $block;
        }

        $description['blocks'] = $blocks;

        return $description;
    }

    private function line(string $line): ?string
    {
        $line = trim((string) preg_replace('/[ \t]+/u', ' ', $line));

        if (
            preg_match('/^_{5,}$/u', $line)
            || preg_match('/^Скачать\s+3d\s+модель(?:\s+в\s+формате\s+stl)?$/iu', $line)
            || preg_match('/^Открыть\s+паспорт\s+на\s+печь$/iu', $line)
            || preg_match('/^↓\s*(?:Полные\s+характеристики\s+печи|Характеристики\s+и\s+комплектация)\s*↓$/iu', $line)
        ) {
            return null;
        }

        $line = trim((string) preg_replace('/\s+подробнее\s*→\s*$/iu', '', $line));

        if (preg_match('/^Гарантия\s+лет$/iu', $line)) {
            return null;
        }

        return $line;
    }
}
