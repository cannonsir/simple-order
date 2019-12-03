<?php

namespace Gtd\SimpleOrder\Models;

use Gtd\SimpleOrder\Exceptions\InvalidAdjustmentAmountException;
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

        static::creating(function (self $adjustment) {
            // 使用调整金额后最终金额不能低于0元
            if (bccomp(bcadd($adjustment->belong->getResAmount(), $adjustment->amount, config('simple-order.decimal_precision.places')), 0) === -1) {
                throw new InvalidAdjustmentAmountException('最终金额不可小于0');
            }
        });

        static::updated(function (self $adjustment) {
            // 修改金额或引入状态后，重新计算订单金额
            $adjustment->wasChanged(['included', 'amount']) and $adjustment->getOrder()->calculator();
        });

        static::created(function (self $adjustment) {
            // 重新计算订单金额
            $adjustment->getOrder()->calculator();
        });

        static::deleted(function (self $adjustment) {
            // 重新计算订单金额
            $adjustment->getOrder()->calculator();
        });
    }

    /**
     * 获取所属模型
     *
     * @return BelongsTo
     */
    public function belong(): BelongsTo
    {
        if ($this->order_id) {
            $related = config('simple-order.models.Order');
            $foreignKey = 'order_id';
        } elseif ($this->order_item_id) {
            $related = config('simple-order.models.OrderItem');
            $foreignKey = 'order_item_id';
        } elseif ($this->order_item_unit_id) {
            $related = config('simple-order.models.OrderItemUnit');
            $foreignKey = 'order_item_unit_id';
        } else {
            $related = null;
            $foreignKey = null;
        }

        return $this->belongsTo($related, $foreignKey);
    }

    /**
     * 获取订单
     *
     * @return mixed|null
     */
    public function getOrder()
    {
        $belong = $this->belong()->first();

        $belongClass = get_class($belong);

        if ($belongClass === config('simple-order.models.Order')) {
            return $belong;
        } elseif ($belongClass === config('simple-order.models.OrderItem')) {
            return $belong->order;
        } elseif ($belongClass === config('simple-order.models.OrderItemUnit')) {
            return $belong->orderItem->order;
        }

        return null;
    }

    public function markAsIncluded()
    {
        $this->update(['included' => true]);
    }

    public function markAsUnIncluded()
    {
        $this->update(['included' => false]);
    }
}