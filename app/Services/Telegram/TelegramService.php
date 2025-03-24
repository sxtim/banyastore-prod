<?php

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    const API_BASE_URL = 'https://api.telegram.org/bot';

    private string $apiUrl;

    public function __construct(string $token)
    {
        $this->apiUrl = self::API_BASE_URL . $token . '/';
    }


    public function sendMessage(int $chatId, string $text, array $params = []): array
    {
        $params['chat_id'] = $chatId;
        $params['text'] = $text;
        $params['parse_mode'] = 'html';
        return $this->request('sendMessage', $params);
    }

    public function setWebhookUrl(string $url): array
    {
        return $this->request('setWebhook', [
            'url' => $url
        ]);
    }

    public function getWebhookUrl(): array
    {
        return $this->request('getWebhookInfo', []);
    }

    private function request(string $method, ?array $data = null): array
    {
        $url = $this->apiUrl . $method;
//
//        $httpClient = new CurlHttpClient();
//        $response = $httpClient->request('POST', $url, [
//            'json' => $data
//        ]);

        try {
            return Http::withHeaders([
                'Accept' => 'application/json',
            ])->post($url, $data)->collect()->toArray();

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
