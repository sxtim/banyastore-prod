<?php

namespace App\Providers;

use App\Services\Payment\Alfa\AlfaService;
use Illuminate\Support\ServiceProvider;

class AlfaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(AlfaService::class, function () {
            return new AlfaService(
                config('alfabank.alfa_user_name'),
                config('alfabank.alfa_password'),
                config('alfabank.alfa_url')
            );
        });
    }
}
