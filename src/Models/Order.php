<?php

namespace Gtd\SimpleOrder\Models;

use Gtd\SimpleOrder\Traits\HasAmount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model implements \Gtd\SimpleOrder\Contracts\Order
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

        $this->setTable(config('simple-order.table_names.orders'));
    }

    /*
     * =====================
     * Relations
     * =====================
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
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
}
