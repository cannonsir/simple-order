<?php

namespace Gtd\SimpleOrder;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/simple-order.php', 'simple-order');
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/2020_12_01_121212_create_order_tables.php');

        $this->publishes([
            __DIR__.'../database/migrations/' => database_path('migrations')
        ], 'simple-order-migrations');
    }
}