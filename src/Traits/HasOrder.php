<?php

namespace Gtd\SimpleOrder\Traits;

use Gtd\SimpleOrder\Models\Order;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

// user will use this trait
trait HasOrder
{
    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'user');
    }

    public function createOrder()
    {

    }
}