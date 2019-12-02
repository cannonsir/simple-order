<?php

namespace Gtd\SimpleOrder\Models;

use Gtd\SimpleOrder\Contracts\OrderContract;
use Gtd\SimpleOrder\Events\OrderCreated;
use Gtd\SimpleOrder\Exceptions\OrderLockedException;
use Gtd\SimpleOrder\Traits\HasAdjustments;
use Gtd\SimpleOrder\Traits\HasAmount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Order extends Model implements OrderContract
{
    use HasAmount, HasAdjustments;

    protected $guarded = ['id'];

    /*
     * 订单号生成器
     */
    protected static $numberGenerate;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('simple-order.table_names.orders'));
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $order) {
            $order->number = self::generateNumber();
        });

        static::created(function (self $order) {
            event(new OrderCreated($order));
            $order->amount()->create();
        });

        static::updating(function (self $order) {
            $order->syncChanges();

            // 订单确认后就不能修改了，最多只能维护状态字段
            if ($order->isInitialized() && array_keys($order->changes) !== ['state']) {
                throw new OrderLockedException;
            }
        });

        static::updated(function (self $order) {
            if ($order->wasChanged('state')) {
                // 推送状态修改事件
            }
        });
    }

    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function childrenDeep(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->with('childrenDeep');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /*
     * 是否初始化完成
     */
    public function isInitialized(): bool
    {
        return $this->exists && $this->getOriginal('state') && $this->state !== self::STATE_INITIALIZING;
    }

    /**
     * 通过订单号查找订单
     *
     * @param string $number
     * @return OrderContract
     */
    public static function findByNumber(string $number): OrderContract
    {
        return static::whereNumber($number)->first();
    }

    /**
     * 生成订单编号
     *
     * @return mixed
     */
    public static function generateNumber()
    {
        return self::getNumberGenerate()();
    }

    /**
     * 设置订单编号生成器
     *
     * @param callable $callable
     */
    public static function setNumberGenerate(callable $callable)
    {
        static::$numberGenerate = $callable;
    }

    /**
     * 获取订单编号生成器
     *
     * @return callable
     */
    public static function getNumberGenerate(): callable
    {
        return self::$numberGenerate ?: function() {
            return Str::random(50);
        };
    }

    /**
     * 计算订单金额
     */
    public function calculator()
    {
        $whereIncluded = function(HasMany $many) {
            $many->whereIncluded(true); // 排除无需纳入计算的调整金额
        };

        // 加载金额有关数据
        $this->load([
            'amount',
            'adjustments' => $whereIncluded,
            'items.amount',
            'items.adjustments' => $whereIncluded,
            'items.units.amount',
            'items.units.adjustments' => $whereIncluded
        ]);

        $items_total = $this->items->reduce(function ($carry, OrderItem $item) {
            $units_total = $item->units->reduce(function ($carry, OrderItemUnit $unit) use ($item) {
                $unit->setAdjustmentsTotal($unit->calculateAdjustmentsTotal());
                $unit->setOriginAmount($item->getUnitPrice());
                $unit->setResAmount($unit->calculateResAmount());

                return bcadd($carry, $unit->getResAmount());
            }, 0);

            // 计算各子商品项目金额
            $item->setAdjustmentsTotal($item->calculateAdjustmentsTotal());
            $item->setOriginAmount($units_total);
            $item->setResAmount($item->calculateResAmount());

            return bcadd($carry, $item->getResAmount());
        }, 0);

        // 计算订单总额
        $this->setAdjustmentsTotal($this->calculateAdjustmentsTotal());
        $this->setOriginAmount($items_total);
        $this->setResAmount($this->calculateResAmount());
    }

    /**
     * 新增商品项目
     *
     * @param $orderable array|Model 商品数据
     * @param $orderable_unit_price string|integer 商品单价
     * @param int $quantity 商品数量
     * @return false|Model
     */
    public function addItem($orderable, $orderable_unit_price, int $quantity = 1)
    {
        $attributes = compact('orderable_unit_price', 'quantity');

        if ($orderable instanceof Model) {
            $attributes['orderable_id'] = $orderable->getKey();
            $attributes['orderable_type'] = $orderable->getMorphClass();
            $attributes['orderable_origin'] = $orderable->toArray();
        } elseif (is_array($orderable)) {
            $attributes['orderable_origin'] = $orderable;
        }

        return $this->items()->save(new OrderItem($attributes));
    }

    /**
     * 设置订单状态
     *
     * @param $state
     * @return bool
     */
    public function setState($state)
    {
        return $this->update(compact('state'));
    }

    /**
     * 标记订单状态为待结账
     */
    public function markAsCheckout()
    {
        $this->update(['state' => self::STATE_CHECKOUT]);
    }

    /**
     * 标记订单状态为已确认
     */
    public function markAsConfirmed()
    {
        $this->update(['state' => self::STATE_CONFIRMED]);
    }

    /**
     * 标记订单状态为已取消
     */
    public function markAsCancelled()
    {
        $this->update(['state' => self::STATE_CANCELLED]);
    }

    /**
     * 标记订单状态为已完成
     */
    public function markAsFulfilled()
    {
        $this->update(['state' => self::STATE_FULFILLED]);
    }

}
