<?php

namespace Gtd\Order\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public function orderable()
    {
        return $this->morphTo('orderable');
    }

    public function units()
    {
        return $this->hasMany(OrderItemUnit::class);
    }
}