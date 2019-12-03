<?php

namespace Gtd\SimpleOrder\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface OrderItemUnitContract extends HasAdjustmentsContract, HasAmountContract
{
    /**
     * 获取所属项目
     *
     * @return BelongsTo
     */
    public function orderItem(): BelongsTo;
}