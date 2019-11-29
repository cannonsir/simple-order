<?php

namespace Gtd\SimpleOrder\Models;

use Gtd\SimpleOrder\Contracts\OrderContract;
use Gtd\SimpleOrder\Events\OrderCreated;
use Gtd\SimpleOrder\Traits\HasAmount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model implements OrderContract
{
    use HasAmount;

    /*
     * 订单状态
     */
    const STATE_INITIALIZE = 'initialize';  // 初始化
    const STATE_CHECKOUT = 'checkout';      // 待结账
    const STATE_CONFIRMED = 'confirmed';    // 已确认
    const STATE_CANCELLED = 'cancelled';    // 已取消
    const STATE_FULFILLED = 'fulfilled';    // 已完成

    public static $states = [
        self::STATE_INITIALIZE,
        self::STATE_CHECKOUT,
        self::STATE_CONFIRMED,
        self::STATE_CANCELLED,
        self::STATE_FULFILLED,
    ];

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

        static::created(function (self $order) {
            event(new OrderCreated($order));
            $order->amount()->create();
        });

        static::saving(function (self $order) {
            // 订单确认后就不能修改了，或者只能修改收货地址信息
            if ($order->exists) {
                throw new \RuntimeException('不可修改');
            }
        });
    }

    /*
     * =====================
     * Relations
     * =====================
     */

//    public function user(): BelongsTo
//    {
//        // TODO 无用户
//        return $this->belongsTo(null);
//    }

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
     * =====================
     * Handles
     * =====================
     */

    /*
     * rewrite save
     */
    public function save(array $options = [])
    {
        // 生成订单编号
        $this->number ?: $this->generateNumber();

        return parent::save($options);
    }

    /*
     * 生成订单编号
     */
    public function generateNumber()
    {
        $this->number = self::getNumberGenerate()($this);
    }

    /*
     * 设置订单编号生成器
     */
    public static function setNumberGenerate(callable $callable)
    {
        static::$numberGenerate = $callable;
    }

    /*
     * 获取订单编号生成器
     */
    public static function getNumberGenerate(): callable
    {
        return self::$numberGenerate ?: function() {
            return Str::random(50);
        };
    }

    /*
     * 由下至上计算全部金额
     */
    public function calculator()
    {
        $whereIncluded = function(HasMany $many) {
            $many->whereIncluded(true); // 排除无需纳入计算的调整金额
        };

        $this->load([
            'amount.adjustments' => $whereIncluded,
            'items.amount.adjustments' => $whereIncluded,
            'items.units.amount.adjustments' => $whereIncluded,
        ]);

        $items_total = $this->items->reduce(function ($carry, OrderItem $item) {
            $units_total = $item->units->reduce(function ($carry, OrderItemUnit $unit) use ($item) {
                $unit->setAdjustmentsTotal($unit->calculateAdjustmentsTotal());
                $unit->setOriginAmount($item->getUnitPrice());
                $unit->setResAmount($unit->calculateResAmount());

                return bcadd($carry, $unit->getResAmount());
            }, 0);

            $item->setAdjustmentsTotal($item->calculateAdjustmentsTotal());
            $item->setOriginAmount($units_total);
            $item->setResAmount($item->calculateResAmount());

            return bcadd($carry, $item->getResAmount());
        }, 0);

        $this->setAdjustmentsTotal($this->calculateAdjustmentsTotal());
        $this->setOriginAmount($items_total);
        $this->setResAmount($this->calculateResAmount());
    }

    /*
     * 关闭订单 TODO 下单，修改状态等操作，需抛出事件
     */
    public function close()
    {
        // state
    }

    /*
     * 确认收货
     */
    public function confirmReceipt()
    {

    }

    /*
     * 设置订单状态
     */
    public function setState($state)
    {
        $this->update(compact('state'));
    }

    /*
     * TODO 订单拆分
     */
    public function split(callable $callable)
    {
        // 传递闭包，返回查询构造器，查询子项目，返回的子项目分离出来
    }
}
