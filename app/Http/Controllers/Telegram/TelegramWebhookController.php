<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TelegramWebhookController extends Controller
{
    public function index(
        Request $request,
        TelegramBotService $service
    ): Response
    {
        $service->webhook($request->all());
        return response('', 200);
    }

    public function newWebhook(
        TelegramService $service
    ): Response
    {
        $d = $service->setWebhookUrl('https://banyastore.ru/telegram-webhook');
        dd($d);
        return response('', 200);
    }
}
