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
        'orders' => 'orders',
        // 订单项目
        'order_items' => 'order_items',
        // 订单项目子单位
        'order_item_units' => 'order_item_units',
        // 金额
        'amounts' => 'amounts',
        // 调整金额
        'adjustments' => 'adjustments',
    ],

    'models' => [
        'Order' => \Gtd\Order\Models\Order::class,
        'OrderItem' => \Gtd\Order\Models\OrderItem::class,
        'OrderItemUnit' => \Gtd\Order\Models\OrderItemUnit::class,
        'Amount' => \Gtd\Order\Models\Amount::class,
        'Adjustment' => \Gtd\Order\Models\Adjustment::class,
    ],
];
