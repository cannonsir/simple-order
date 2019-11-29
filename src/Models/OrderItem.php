<?php

namespace Gtd\SimpleOrder\Models;

use Gtd\SimpleOrder\Contracts\OrderAbleContract;
use Gtd\SimpleOrder\Traits\HasAmount;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasAmount;

    protected $guarded = ['id'];

    protected $hidden = ['orderable_serialize'];

    protected static function boot()
    {
        parent::boot();

        static::created(function (self $item) {
            // 总金额 = 商品单价 * 数量
            $item->amount()->create(['should_amount' => \bcmul($item->getUnitPrice(), $item->quantity)]);

            // 生成对应数量的最小单位unit
            for ($i = 0; $i <= $item->quantity; $i++) {
                $item->units()->save(new OrderItemUnit);
            }
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

    public function units()
    {
        return $this->hasMany(OrderItemUnit::class, 'item_id');
    }

    /*
     * 获取项目单价
     */
    public function getUnitPrice()
    {
        return $this->getOriginOrderable()->getAmount();
    }

    /*
     * 获取项目快照
     */
    public function getOriginOrderable(): OrderAbleContract
    {
        return unserialize($this->orderable_serialize);
    }
}