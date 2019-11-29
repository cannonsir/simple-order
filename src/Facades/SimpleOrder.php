<?php

namespace Gtd\SimpleOrder\Facades;

use Gtd\SimpleOrder\Contracts\OrderContract;
use Illuminate\Support\Facades\Facade;

class SimpleOrder extends Facade
{
    protected static function getFacadeAccessor()
    {
        return OrderContract::class;
    }
}