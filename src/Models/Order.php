<?php

namespace Gtd\Order\Models;

use Gtd\Order\Traits\HasAmount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model implements \Gtd\Order\Contracts\Order
{
    use HasAmount;

    protected $guarded = ['id'];

    /*
     * 订单号生成器
     */
    protected static $numberGenerate;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('order.table_names.orders'));
    }

    public static function create(array ...$attributes)
    {
        $order = parent::create($attributes);

        // 生成订单编号
        $order->number ?: $order->generateNumber();

        return $order;
    }

    public function save(array $options = [])
    {
        // 生成订单编号
        $this->number ?: $this->generateNumber();

        return parent::save($options);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addItem()
    {

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
     * 关闭订单 TODO 下单，修改状态等操作，需抛出事件
     */
    public function close()
    {
        // state
    }

    /*
     * 删除订单
     */
    public function delete()
    {
        return parent::delete();
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
    public function setState()
    {

    }

    /*
     * 设置运输状态
     */
    public function setShipmentState()
    {

    }

    /*
     * 设置支付状态
     */
    public function setPaymentState()
    {

    }

}
