<?php

namespace Gtd\SimpleOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItemUnit extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('simple-order.table_names.order_item_units'));
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function (self $unit) {

        });
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function amount(): HasOne
    {
        return $this->hasOne(Amount::class, 'order_item_unit_id');
    }
}