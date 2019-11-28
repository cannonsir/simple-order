<?php

namespace Gtd\Order\Traits;

use Gtd\Order\Models\Order;
use Illuminate\Database\Eloquent\Relations\HasMany;

// user will use this trait
trait HasOrder
{
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}