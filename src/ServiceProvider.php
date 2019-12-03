<?php

namespace Gtd\SimpleOrder;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/simple-order.php', 'simple-order');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/simple-order.php' => config_path('simple-order.php')
            ], 'simple-order-config');

            $this->publishes([
                __DIR__.'/../database/migrations/create_order_tables.php.stub' => $this->getMigrationFileName()
            ], 'simple-order-migrations');
        }
    }

    protected function getMigrationFileName()
    {
        $timestamp = date('Y_m_d_His');

        return Collection::make($this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) {
                return (new Filesystem)->glob($path.'*_create_order_tables.php');
            })
            ->push($this->app->databasePath()."/migrations/{$timestamp}_create_order_tables.php")
            ->first();
    }
}