<?php

namespace Gtd\SimpleOrder\Contracts;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasAdjustmentsContract
{
    /**
     * @return HasMany 调整金额列表
     */
    public function adjustments(): HasMany;

    /**
     * 添加调整金额
     *
     * @param string $label 标注
     * @param int $amount 金额
     * @return mixed
     */
    public function addAdjustment(string $label, $amount = 0);

    /**
     * 添加不纳入计算的调整金额
     *
     * @param string $label 标注
     * @param int $amount 金额
     * @return mixed
     */
    public function addUnIncludedAdjustment(string $label, $amount = 0);
}