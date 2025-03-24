<?php

namespace App\Services;


use App\Models\Order\Order;
use App\Services\Telegram\TelegramService;

class NotificationService
{
    /**
     * Список айди чатов в которые отправить уведомление о новом заказе
     */
    const TELEGRAM_CHATS = [
        246607398
    ];

    public function __construct(
        private TelegramService $telegramService
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function newOrder(Order $order): void
    {
        $text = 'Новый заказ №' . $order->id;
        foreach (self::TELEGRAM_CHATS as $chatId) {
            $this->telegramService->sendMessage($chatId,$text);
        }
    }
}
