<?php

namespace App\Services\Feed;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class IronSteelFeedClient
{
    public function fetch(string $url): string
    {
        $parts = parse_url($url);
        $host = strtolower((string) ($parts['host'] ?? ''));
        $allowedHosts = array_map('strtolower', config('feed_import.http.allowed_feed_hosts', []));
        if (
            ! filter_var($url, FILTER_VALIDATE_URL)
            || ($parts['scheme'] ?? null) !== 'https'
            || $host === ''
            || ! in_array($host, $allowedHosts, true)
            || isset($parts['user'])
            || isset($parts['pass'])
            || isset($parts['port'])
        ) {
            throw new FeedException('Некорректный или неразрешённый адрес фида.');
        }

        try {
            $response = Http::connectTimeout(config('feed_import.http.connect_timeout'))
                ->timeout(config('feed_import.http.timeout'))
                ->retry(config('feed_import.http.retries'), 500)
                ->accept('application/xml, text/xml, application/yaml, text/yaml')
                ->withOptions(['allow_redirects' => false])
                ->get($url);
        } catch (ConnectionException $exception) {
            throw new FeedException('Не удалось подключиться к фиду: '.$exception->getMessage(), 0, $exception);
        }

        if (! $response->successful()) {
            throw new FeedException('Фид вернул HTTP '.$response->status().'.');
        }

        $body = $response->body();
        if ($body === '') {
            throw new FeedException('Фид вернул пустой ответ.');
        }

        if (strlen($body) > config('feed_import.http.max_feed_bytes')) {
            throw new FeedException('Размер фида превышает допустимый лимит.');
        }

        return $body;
    }
}
