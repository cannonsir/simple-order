<?php

namespace Gtd\SimpleOrder;

use Gtd\SimpleOrder\Contracts\SimpleOrderContract;
use Gtd\SimpleOrder\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;

class SimpleOrder implements SimpleOrderContract
{
    protected $order;

    public $user;

    public $items = [];

    // TODO 现在为自动创建，由此类代理创建后是否还需要自动创建？
    public $units = [];

    public function setUser(Model $user): SimpleOrderContract
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem($orderable, $orderable_unit_price, int $quantity = 1): SimpleOrderContract
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

    public function setOrderAdjustments(callable $callable)
    {
        // 通过此方法接受一个闭包，在闭包内传递订单实例，然后由闭包返回调整实例或某种固定格式的数据，好用来查看价格
        // 或者退款时的有迹可循
    }

    public function setItemsAdjustments(callable $callable)
    {

    }

    public function push()
    {
        // push 保存订单，及所有项目，单位，返回值是订单实例还是布尔呢
    }

    public function __destruct()
    {
        // TODO: 删除状态为初始化的订单(self::$order)
    }
}