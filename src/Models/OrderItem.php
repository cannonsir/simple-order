<?php

namespace Gtd\SimpleOrder\Models;

use Gtd\SimpleOrder\Exceptions\InvalidAmountException;
use Gtd\SimpleOrder\Exceptions\InvalidQuantityException;
use Gtd\SimpleOrder\Traits\HasAdjustments;
use Gtd\SimpleOrder\Traits\HasAmount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasAmount, HasAdjustments;

    protected $guarded = ['id'];

    protected $casts = [
        'orderable_origin' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (self $item) {
            if ($item->quantity < 1) {
                throw new InvalidQuantityException;
            }

            if ($item->getUnitPrice() < 0) {
                throw new InvalidAmountException;
            }
        });

        static::updated(function (self $item) {
            if ($item->wasChanged('quantity')) {
                $item->resetUnits();
            }

            $item->wasChanged(['orderable_unit_price', 'quantity']) && $item->order->calculator();
        });

        static::created(function (self $item) {
            $item->amount()->create();

            $item->resetUnits();

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
        return $this->belongsTo(config('simple-order.models.Order'), 'order_id');
    }

    public function units()
    {
        return $this->hasMany(config('simple-order.models.OrderItemUnit'), 'item_id');
    }

    /**
     * 更新单价
     *
     * @param $orderable_unit_price
     */
    public function updateUnitPrice($orderable_unit_price)
    {
        $this->update(compact('orderable_unit_price'));
    }

    /**
     * 更新数量
     *
     * @param integer $quantity
     */
    public function updateQuantity(int $quantity)
    {
        $this->update(compact('quantity'));
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

    /**
     * 创建对应数量的最小单位unit
     */
    protected function resetUnits()
    {
        $this->units()->delete();

        $orderItemUnitClass = config('simple-order.models.OrderItemUnit');

        for ($i = 1; $i <= $this->quantity; $i++) {
            $this->units()->save(new $orderItemUnitClass);
        }
    }
}