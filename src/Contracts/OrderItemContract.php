<?php

namespace Gtd\SimpleOrder\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface OrderItemContract extends HasAdjustmentsContract, HasAmountContract
{
    /**
     * 获取商品实例
     *
     * @return MorphTo
     */
    public function orderable(): MorphTo;

    /**
     * 获取所属订单
     *
     * @return BelongsTo
     */
    public function order(): BelongsTo;

    /**
     * 获取子单位列表
     *
     * @return HasMany
     */
    public function units(): HasMany;

    /**
     * 获取项目单价
     *
     * @return mixed
     */
    public function getUnitPrice();

    /**
     * 获取项目商品快照
     *
     * @return mixed
     */
    public function getOriginOrderable();
}