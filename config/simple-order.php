<?php

return [
    // 金额精度
    'decimal_precision' => [
        'total' => 10,
        'places' => 2,
    ],

    // 表名映射
    'table_names' => [
        // 订单
        'orders' => 'order_orders',
        // 订单项目
        'order_items' => 'order_order_items',
        // 订单项目子单位
        'order_item_units' => 'order_order_item_units',
        // 金额
        'amounts' => 'order_amounts',
        // 调整金额
        'adjustments' => 'order_adjustments',
    ],

    'models' => [
        // implements Gtd\SimpleOrder\Contracts\OrderContract
        'Order' => \Gtd\SimpleOrder\Models\Order::class,

        // implements Gtd\SimpleOrder\Contracts\OrderItemContract
        'OrderItem' => \Gtd\SimpleOrder\Models\OrderItem::class,

        // implements Gtd\SimpleOrder\Contracts\OrderItemUnitContract
        'OrderItemUnit' => \Gtd\SimpleOrder\Models\OrderItemUnit::class,

        // implements Gtd\SimpleOrder\Contracts\AmountContract
        'Amount' => \Gtd\SimpleOrder\Models\Amount::class,

        // implements Gtd\SimpleOrder\Contracts\AdjustmentContract
        'Adjustment' => \Gtd\SimpleOrder\Models\Adjustment::class,
    ],
];
