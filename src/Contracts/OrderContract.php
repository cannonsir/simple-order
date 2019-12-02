<?php

namespace Gtd\SimpleOrder\Contracts;

interface OrderContract
{
    /*
     * 订单状态
     */
    const STATE_INITIALIZING = 'initializing';  // 初始化
    const STATE_CHECKOUT = 'checkout';      // 待结账
    const STATE_CONFIRMED = 'confirmed';    // 已确认
    const STATE_CANCELLED = 'cancelled';    // 已取消
    const STATE_FULFILLED = 'fulfilled';    // 已完成
}