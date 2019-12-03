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
        'Order' => \Gtd\SimpleOrder\Models\Order::class,
        'OrderItem' => \Gtd\SimpleOrder\Models\OrderItem::class,
        'OrderItemUnit' => \Gtd\SimpleOrder\Models\OrderItemUnit::class,
        'Amount' => \Gtd\SimpleOrder\Models\Amount::class,
        'Adjustment' => \Gtd\SimpleOrder\Models\Adjustment::class,
    ],
];
