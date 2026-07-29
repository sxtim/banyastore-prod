<?php

namespace App\Services\Feed;

class FeedDescriptionFactory
{
    public function make(string $html, array $packagingLines = []): ?array
    {
        $text = $this->plainText($html);
        $packagingLines = array_values(array_filter(array_map(
            fn ($line) => $this->plainText((string) $line),
            $packagingLines
        )));
        $blocks = [];

        if ($text !== '') {
            $blocks[] = $this->paragraph($text);
        }

        if ($packagingLines !== []) {
            $packagingText = implode("\n", $packagingLines);
            $blocks[] = [
                'id' => substr(hash('sha256', 'Упаковка'.$packagingText), 0, 10),
                'type' => 'header',
                'data' => [
                    'text' => 'Упаковка',
                    'level' => 3,
                ],
            ];
            $blocks[] = $this->paragraph($packagingText);
        }

        if ($blocks === []) {
            return null;
        }

        return [
            'time' => now()->getTimestampMs(),
            'blocks' => $blocks,
            'version' => '2.27.2',
        ];
    }

    private function plainText(string $html): string
    {
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $html = preg_replace('/<\s*br\s*\/?\s*>/iu', "\n", $html);
        $html = preg_replace('/<\s*\/?\s*(p|ul|ol)\b[^>]*>/iu', "\n", $html);
        $html = preg_replace('/<\s*li\b[^>]*>/iu', '• ', $html);
        $html = preg_replace('/<\s*\/\s*li\s*>/iu', "\n", $html);
        $text = trim(strip_tags((string) $html));
        $text = preg_replace("/[ \t]+\n/u", "\n", $text);
        $text = preg_replace("/\n{3,}/u", "\n\n", (string) $text);

        return $text;
    }

    private function paragraph(string $text): array
    {
        return [
            'id' => substr(hash('sha256', $text), 0, 10),
            'type' => 'paragraph',
            'data' => [
                'text' => nl2br(e($text), false),
            ],
        ];
    }
}
