<?php

namespace Gtd\SimpleOrder\Models;

use Gtd\SimpleOrder\Traits\HasAdjustmentsAmount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Amount extends Model
{
    use HasAdjustmentsAmount;

    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('simple-order.table_names.amounts'));
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function (self $self) {
            if (isset($self->amount) && !is_numeric($self->amount) || $self->amount < 0) {
                throw new \RuntimeException('商品金额非法');
            }
        });
    }

    public function belong(): BelongsTo
    {
        switch (true) {
            case $this->order_id :
                $related = Order::class;
                $foreignKey = 'order_id';
                break;
            case $this->order_item_id :
                $related = OrderItem::class;
                $foreignKey = 'order_item_id';
                break;
            case $this->order_item_unit_id :
                $related = OrderItemUnit::class;
                $foreignKey = 'order_item_unit_id';
                break;
            default :
                $related = null;
                $foreignKey = null;
        }

        return $this->belongsTo($related, $foreignKey);
    }
}