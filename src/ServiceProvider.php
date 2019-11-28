<?php

namespace Gtd\Order;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        dd(233);
        $this->mergeConfigFrom(__DIR__.'../config/order.php', 'order');
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '../database/migrations/2020_12_01_121212_create_order_tables.php');

        $this->publishes([
            __DIR__.'../database/migrations/' => database_path('migrations')
        ], 'order-migrations');
    }
}