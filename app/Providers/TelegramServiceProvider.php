<?php

namespace App\Providers;

use App\Services\Telegram\TelegramService;
use Illuminate\Support\ServiceProvider;

class TelegramServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(TelegramService::class, function () {
            return new TelegramService(
                config('telegram.telegram_token')
            );
        });
    }
}
