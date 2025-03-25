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
        $text = 'Новый заказ №' . $order->id . "\n\n";
        $text .= 'Состав заказа:';
        $text .= " \n";
        foreach ($order->listProducts() as $product) {
            $text .= $product['name'] . ' - ' . $product['quantity'] . 'шт.' . " \n";
        }
        $text .= " \n";
        $text .= 'Сумма заказа: ' . $order->price .'р.' . " \n\n";
        $text .= 'Информация о клиенте:';
        $text .= " \n";
        $text .= 'Имя: ' . $order->name . " \n";
        $text .= 'Телефон: ' . $order->phone . " \n";
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
        $text = 'Новое сообщение по обратной связи:';
        $text .= " \n";
        $text .= 'Имя:' . $name . " \n";
        $text .= 'Телефон:' . $phone . " \n";
        $text .= 'Сообщение:' . $message;

        foreach (self::TELEGRAM_CHATS as $chatId) {
            $this->telegramService->sendMessage($chatId,$text);
        }
    }
}
