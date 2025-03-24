<?php

namespace App\Services\Telegram;

use App\Models\Telegram\TelegramChat;

class TelegramBotService
{

    public function __construct(
    )
    {
    }

    public function webhook(array $params): void
    {
        if (isset($params['message']['text']) && isset($params['message']['from'])) {
            TelegramChat::create([
                'chat_id' => $params['message']['from']['id'],
                'username' => $params['message']['from']['username'],
                'name' => $params['message']['from']['last_name'].' '.$params['message']['from']['first_name'],
                'message' => $params['message']['text']
            ]);
        }
    }
}
