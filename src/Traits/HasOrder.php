<?php

namespace Gtd\SimpleOrder\Traits;

use Gtd\SimpleOrder\Models\Order;
use Illuminate\Database\Eloquent\Relations\HasMany;

// user will use this trait
trait HasOrder
{
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}