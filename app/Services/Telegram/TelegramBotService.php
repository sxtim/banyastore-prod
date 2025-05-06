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
            $userName = isset($params['message']['from']['username']) ?? 'Ник скрыт';
            $firstName = isset($params['message']['from']['first_name']) ?? 'Имя скрыто';
            $lastName = isset($params['message']['from']['last_name']) ?? 'Фамилия скрыта';
            $fullName = $lastName . ' ' . $firstName;

            TelegramChat::create([
                'chat_id' => $params['message']['from']['id'],
                'username' => $userName,
                'name' => $fullName,
                'message' => $params['message']['text']
            ]);
        }
    }
}
