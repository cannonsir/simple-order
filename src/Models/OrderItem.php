<?php

namespace Gtd\SimpleOrder\Models;

use Gtd\SimpleOrder\Exceptions\InvalidAmountException;
use Gtd\SimpleOrder\Exceptions\OrderItemCannotUpdateException;
use Gtd\SimpleOrder\Exceptions\OrderLockedException;
use Gtd\SimpleOrder\Traits\HasAdjustments;
use Gtd\SimpleOrder\Traits\HasAmount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasAmount, HasAdjustments;

    protected $guarded = ['id'];

    protected $hidden = ['orderable_origin'];

    protected $casts = [
        'orderable_origin' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (self $item) {
            if ($item->order->isInitialized()) {
                throw new OrderLockedException;
            }
        });

        static::updating(function () {
            throw new OrderItemCannotUpdateException;
        });

        static::deleting(function (self $item) {
            if ($item->order->isInitialized()) {
                throw new OrderLockedException;
            }
        });

        static::creating(function (self $item) {
            if ($item->quantity < 1) {
                throw new \InvalidArgumentException('数量最小为1');
            }

            if ($item->getUnitPrice() < 0) {
                throw new InvalidAmountException;
            }
        });

        static::created(function (self $item) {
            $item->amount()->create();

            // 生成对应数量的最小单位unit
            for ($i = 0; $i <= $item->quantity; $i++) {
                $item->units()->save(new OrderItemUnit);
            }

            $item->order->calculator();
        });

        static::deleted(function (self $item) {
            $item->order->calcalator();
        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('simple-order.table_names.order_items'));
    }

    public function orderable()
    {
        return $this->morphTo();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function units()
    {
        return $this->hasMany(OrderItemUnit::class, 'item_id');
    }

    /*
     * 获取项目单价
     */
    public function getUnitPrice()
    {
        return $this->orderable_unit_price;
    }

    /*
     * 获取项目快照
     */
    public function getOriginOrderable()
    {
        return $this->orderable_origin;
    }
}