<?php

namespace Gtd\SimpleOrder\Models;

use Gtd\SimpleOrder\Traits\HasAmount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemUnit extends Model
{
    use HasAmount;

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('simple-order.table_names.order_item_units'));
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function (self $unit) {
            $unit->amount()->create(['should_amount' => $unit->orderItem->getUnitPrice()]);
        });
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'item_id');
    }
}