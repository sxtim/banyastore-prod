<?php

namespace App\Services\Feed;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FeedImageManager
{
    public function prepareAll(array $urls, int $runId, string $offerId): array
    {
        if ($urls === []) {
            throw new FeedImageException('В фиде нет фотографий товара.');
        }

        $files = [];
        try {
            foreach ($urls as $index => $url) {
                $files[] = $this->download($url, $runId, $offerId, $index);
            }
        } catch (\Throwable $exception) {
            $this->deletePrepared($files);
            throw $exception;
        }

        return ['files' => $files, 'warnings' => []];
    }

    public function prepareForNew(array $urls, int $runId, string $offerId): array
    {
        if ($urls === []) {
            throw new FeedImageException('Новый товар нельзя создать без главной фотографии.');
        }

        $files = [$this->download($urls[0], $runId, $offerId, 0)];
        $warnings = [];

        foreach (array_slice($urls, 1, null, true) as $index => $url) {
            try {
                $files[] = $this->download($url, $runId, $offerId, $index);
            } catch (\Throwable $exception) {
                $warnings[] = 'Дополнительное фото '.($index + 1).': '.$exception->getMessage();
            }
        }

        return ['files' => $files, 'warnings' => $warnings];
    }

    public function archiveCurrent(array $paths, int $runId, string $offerId): array
    {
        $backups = [];

        foreach (array_values(array_filter($paths)) as $index => $path) {
            if (! Storage::exists($path)) {
                continue;
            }

            $backup = "feed-imports/rollback/{$runId}/{$offerId}/{$index}-".basename($path);
            Storage::makeDirectory(dirname($backup));
            if (! Storage::copy($path, $backup)) {
                throw new FeedImageException("Не удалось сохранить резервную копию {$path}.");
            }
            $backups[$path] = $backup;
        }

        return $backups;
    }

    public function movePrepared(array $files, string $sourceSlug, string $offerId, int $runId): array
    {
        $paths = [];
        $directory = "public/products/feed/{$sourceSlug}/{$offerId}/{$runId}";
        Storage::makeDirectory($directory);

        try {
            foreach ($files as $index => $file) {
                $target = "{$directory}/".($index + 1).'.'.$file['extension'];
                if (! Storage::move($file['path'], $target)) {
                    throw new FeedImageException('Не удалось переместить загруженную фотографию.');
                }
                $paths[] = $target;
            }
        } catch (\Throwable $exception) {
            Storage::delete(array_merge($paths, array_column($files, 'path')));
            throw $exception;
        }

        return $paths;
    }

    public function deleteOriginals(array $paths): void
    {
        Storage::delete(array_values(array_filter($paths)));
    }

    public function deletePaths(array $paths): void
    {
        Storage::delete(array_values(array_filter($paths)));
    }

    public function discardPrepared(array $files): void
    {
        $this->deletePrepared($files);
    }

    public function restore(array $backups): void
    {
        foreach ($backups as $original => $backup) {
            if (! Storage::exists($backup)) {
                throw new FeedImageException("Не найдена резервная фотография {$backup}.");
            }

            Storage::makeDirectory(dirname($original));
            Storage::delete($original);
            if (! Storage::copy($backup, $original)) {
                throw new FeedImageException("Не удалось восстановить фотографию {$original}.");
            }
        }
    }

    private function download(string $url, int $runId, string $offerId, int $index): array
    {
        $parts = parse_url($url);
        $host = strtolower((string) ($parts['host'] ?? ''));
        $allowedHosts = array_map('strtolower', config('feed_import.http.allowed_image_hosts', []));
        if (
            ! filter_var($url, FILTER_VALIDATE_URL)
            || ($parts['scheme'] ?? null) !== 'https'
            || $host === ''
            || ! in_array($host, $allowedHosts, true)
            || isset($parts['user'])
            || isset($parts['pass'])
            || isset($parts['port'])
        ) {
            throw new FeedImageException('Некорректная ссылка на фотографию.');
        }

        $response = Http::connectTimeout(config('feed_import.http.connect_timeout'))
            ->timeout(config('feed_import.http.timeout'))
            ->retry(config('feed_import.http.retries'), 500)
            ->withOptions(['allow_redirects' => false])
            ->get($url);

        if (! $response->successful()) {
            throw new FeedImageException("Фотография вернула HTTP {$response->status()}.");
        }

        $body = $response->body();
        if ($body === '' || strlen($body) > config('feed_import.http.max_image_bytes')) {
            throw new FeedImageException('Фотография пустая или превышает допустимый размер.');
        }

        $imageInfo = @getimagesizefromstring($body);
        if (! $imageInfo || ! isset($imageInfo['mime']) || ! str_starts_with($imageInfo['mime'], 'image/')) {
            throw new FeedImageException('По ссылке получен файл, который не является изображением.');
        }
        if (
            ($imageInfo[0] ?? 0) < 1
            || ($imageInfo[1] ?? 0) < 1
            || ($imageInfo[0] * $imageInfo[1]) > config('feed_import.http.max_image_pixels')
        ) {
            throw new FeedImageException('Фотография имеет недопустимые размеры.');
        }

        $extension = match ($imageInfo['mime']) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => throw new FeedImageException("Неподдерживаемый формат {$imageInfo['mime']}."),
        };

        $path = "feed-imports/staging/{$runId}/{$offerId}/{$index}-".Str::uuid().".{$extension}";
        Storage::put($path, $body);

        return ['path' => $path, 'extension' => $extension, 'source_url' => $url];
    }

    private function deletePrepared(array $files): void
    {
        Storage::delete(array_column($files, 'path'));
    }
}
