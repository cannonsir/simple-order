<?php

namespace Gtd\Order\Models;

use Gtd\Order\Traits\HasAmount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemUnit extends Model
{
    use HasAmount;

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}