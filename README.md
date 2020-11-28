# **simple-order**

简单的订单模块实现，通过商品单价及数量确认订单数据。
并可对订单某项目的某个单位做单独处理，如金额的调整(优惠券)等，
可自动由下至上计算整个订单金额

注：暂不涉及维护订单状态/运输状态/支付状态

## 安装

引入

```bash
composer require cannonsir/simple-order
```

发布迁移文件

```bash
php artisan vendor:publish --tag=simple-order-migrations
```

[可选]发布配置文件

```bash
php artisan vendor:publish --tag=simple-order-config
```

## 使用

#### 在用户模型引入`HasOrder`Trait

```php
use Illuminate\Database\Eloquent\Model;
use CannonSir\SimpleOrder\Traits\HasOrder;

class User extends Model
{
    use HasOrder;
}
```

#### 创建订单

```php
$order = $user->createOrder();
$order = $user->createOrder($attributes);
```

#### 自定义订单号生成规则

可在`AppServiceProvider`的`boot`方法中添加如下定义:

```php
public function boot()
{
    \CannonSir\SimpleOrder\Models\Order::setNumberGenerate(function () {
        return \Str::random(30);
    });
}
```

#### 增加订单子项目

```php
$item1 = $order->addItem(Product::find(1));
$item2 = $order->addItem(Product::find(2), 5000);
$item3 = $order->addItem(Product::find(3), 5000, 2);
```

`addItem`(添加项目)参数说明:

> 该方法创建订单子项目的同时会创建对应数量的最小单位，方便后续对最小单位的操作

1. `$orderable` 商品详情 数组或者模型实例
2. `$orderable_unit_price` 商品单价, 默认值0
3. `$quantity` 商品数量，默认值1

#### 更新子项目

```php
$item1->update($attributes);
```

更新单价

```php
$item1->updateUnitPrice(100);
```

更新数量

> 注：数量更新后会重新创建单位，之前如果对单位分配了调整金额将会丢失

```php
$item1->updateQuantity(2);
```

#### 设置金额调整

如运费，优惠券，商品税等调整金额

```php
// 作用至订单
$order->addAdjustment('运费', 10);

// 作用至子项目
$item1->addAdjustment('优惠券', '-3');
$item2->addAdjustment('折扣', '-5');
$item3->addUnIncludedAdjustment('商品税', 5);

// 作用至最小单位
$item->units()->first()->addAdjustment('第二件半价', '-10');
```

`addAdjustment` (新增调整金额)参数说明:

1. `$label` 标注，调整金额标注，可传递字符串或模型实例
2. `$amount` 金额, 默认0

`addUnIncludedAdjustment` (新增不纳入最终计算的调整金额)参数说明:

> 此方法新增的调整金额included字段值为false,将不会在计算订单金额时引入，如商品税等金额一般由商家承担，却在小票中需要打印

1. `$label` 标注，调整金额标注，可传递字符串或模型实例
2. `$amount` 金额, 默认0

#### 订单金额计算

> 在对订单的子项目或者最小单位进行 新增，删除，修改都将会影响到订单价格

```php
$order->calculator();
```


#### 查询

获取订单下所有数据

```php
$order->loadTrunk()->toArray();

// 等同于:
$order->load([
    'amount',                   // 金额详情
    'adjustments',              // 调整金额
    'items.amount',             // 子项目金额详情
    'items.adjustments',        // 子项目调整金额
    'items.units.amount',       // 子项目单位金额详情
    'items.units.adjustments'   // 子项目单位调整金额
])->toArray();
```

通过订单号查找订单

```php
use CannonSir\SimpleOrder\Models\Order;

Order::findByNumber($number);
```

获取订单的子项目

```php
$order->items;
```

获取项目的子单位

```php
$item->units;
```

获取 订单/项目/单位 的调整金额

```php
$order->adjustments;
$item->adjustments;
$unit->adjustments;
```

获取 订单/项目/单位 的最终金额

```php
$order->amount;
$item->amount;
$unit->amount;
```

获取 调整金额/最终金额 所属模型

```php
$adjustment->belong;
$amount->belong;
```

## 触发事件

Event | 描述 | 参数 |
-|-|-
OrderCreated | 订单创建完成 | order:订单实例 |

## 完整示例🌰

```php
// 创建订单
$order = $user->createOrder();

// 创建订单项目
$item1 = $order->addItem($product, 100);
$item2 = $order->addItem(['title' => 'foo', 'amount' => 200], 200, 2);
$item3 = $order->addItem($sku, 150, 3);

// 调整金额
$item1->adjustments()->create(['label' => '优惠券', 'amount' => '-10']);
$item2->addAdjustment('超重运费', 10);
$item3->addUnIncludedAdjustment('包邮', '10');
$item3->units()->first()->addAdjustment('第二件半价', '-75');

// 返回订单数据(携带所有子项目及单位)
return $order->loadTrunk();
```
