<?php

namespace Gtd\Order\Models;

use Gtd\Order\Traits\HasAmount;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasAmount;

    public function orderable()
    {
        return $this->morphTo('orderable');
    }

    public function units()
    {
        return $this->hasMany(OrderItemUnit::class);
    }
}