<?php

namespace Gtd\SimpleOrder\Events;

use Gtd\SimpleOrder\Contracts\OrderContract;

class OrderCreated
{
    public $order;

    public function __construct(OrderContract $order)
    {
        $this->order = $order;
    }
}