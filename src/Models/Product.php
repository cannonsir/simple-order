<?php

namespace Gtd\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'orderable');
    }
}