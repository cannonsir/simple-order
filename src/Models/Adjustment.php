<?php

namespace Gtd\SimpleOrder\Models;

use Gtd\SimpleOrder\Exceptions\InvalidAdjustmentAmountException;
use Gtd\SimpleOrder\Exceptions\OrderLockedException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Adjustment extends Model
{
    protected $guarded = ['id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('simple-order.table_names.adjustments'));
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function (self $adjustment) {
            if ($adjustment->getOrder()->isInitialized()) {
                throw new OrderLockedException;
            }
        });

        static::deleting(function (self $adjustment) {
            if ($adjustment->getOrder()->isInitialized()) {
                throw new OrderLockedException;
            }
        });

        static::creating(function (self $adjustment) {
            if (bccomp(bcadd($adjustment->belong->getResAmount(), $adjustment->amount), 0) === -1) {
                throw new InvalidAdjustmentAmountException('最终金额不得小于0');
            }
        });

        static::created(function (self $adjustment) {
            $adjustment->getOrder()->calculator();
        });

        static::deleted(function (self $adjustment) {
            $adjustment->getOrder()->calculator();
        });
    }

    public function belong(): BelongsTo
    {
        if ($this->order_id) {
            $related = Order::class;
            $foreignKey = 'order_id';
        } elseif ($this->order_item_id) {
            $related = OrderItem::class;
            $foreignKey = 'order_item_id';
        } elseif ($this->order_item_unit_id) {
            $related = OrderItemUnit::class;
            $foreignKey = 'order_item_unit_id';
        } else {
            $related = null;
            $foreignKey = null;
        }

        return $this->belongsTo($related, $foreignKey);
    }

    public function getOrder()
    {
        $belong = $this->belong;

        switch (true) {
            case $belong instanceof Order :
                return $belong;
                break;
            case $belong instanceof OrderItem :
                return $belong->order;
                break;
            case $belong instanceof OrderItemUnit :
                return $belong->orderItem->order;
                break;
            default :
                return null;
        }
    }
}