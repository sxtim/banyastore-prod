<?php

namespace App\Providers;

use App;
use App\Services\Basket\BasketInterface;
use App\Services\Basket\DatabaseBasketService;
use App\Services\Basket\SessionBasketService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class BasketServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(BasketInterface::class, function (Application $app) {
            if (auth()->check()) {
                return new DatabaseBasketService();
            } else {
                return new SessionBasketService();
            }
        });
    }
}
