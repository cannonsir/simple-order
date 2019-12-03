<?php

namespace Gtd\SimpleOrder\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;

// user will use this trait
trait HasOrder
{
    public function orders(): MorphMany
    {
        return $this->morphMany(config('simple-order.models.Order'), 'user');
    }

    public function createOrder(array $attributes = [])
    {
        return $this->orders()->create($attributes);
    }
}