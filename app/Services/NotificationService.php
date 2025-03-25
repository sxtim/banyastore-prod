<?php

namespace App\Services;


use App\Models\Order\Order;
use App\Services\Telegram\TelegramService;

class NotificationService
{
    /**
     * Список айди чатов в которые отправить уведомление
     */
    const TELEGRAM_CHATS = [
        246607398,
        857880259
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
        $text = 'Новый заказ №' . $order->id . '</br></br>';
        $text .= 'Состав заказа:</br>';
        foreach ($order->listProducts() as $product) {
            $text .= $product['name'] . ' - ' . $product['quantity'] . 'шт.</br>';
        }
        $text .= '</br>Сумма заказа: ' . $order->price .'р.</br></br>';
        $text .= 'Информация о клиенте:</br>';
        $text .= 'Имя: ' . $order->name . '</br>';
        $text .= 'Телефон: ' . $order->phone . '</br>';
        foreach (self::TELEGRAM_CHATS as $chatId) {
            $this->telegramService->sendMessage($chatId,$text);
        }
    }

    /**
     * @throws \Exception
     */
    public function newFeedback(
        string $name,
        string $phone,
        string $message
    ): void
    {
        $text = 'Новое сообщение по обратной связи:</br>';
        $text .= 'Имя:' . $name . '</br>';
        $text .= 'Телефон:' . $phone . '</br>';
        $text .= 'Сообщение:' . $message;

        foreach (self::TELEGRAM_CHATS as $chatId) {
            $this->telegramService->sendMessage($chatId,$text);
        }
    }
}
