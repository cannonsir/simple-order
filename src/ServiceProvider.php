<?php

namespace Gtd\Order;

use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider
{
    public function register()
    {

    }

    public function asOrder()
    {
        //
    }


    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'../database/migrations/create_order_tables.php');
    }
}