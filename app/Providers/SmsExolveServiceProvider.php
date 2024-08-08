<?php

namespace App\Providers;

use App\Services\Payment\Alfa\AlfaService;
use App\Services\SmsExolveService;
use Illuminate\Support\ServiceProvider;

class SmsExolveServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(SmsExolveService::class, function () {
            return new SmsExolveService(
                config('exolve.url'),
                config('exolve.api_key')
            );
        });
    }
}
