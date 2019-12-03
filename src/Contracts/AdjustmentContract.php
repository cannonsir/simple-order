<?php


namespace Gtd\SimpleOrder\Contracts;


use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface AdjustmentContract
{
    /**
     * 所属模型
     *
     * @return BelongsTo
     */
    public function belong(): BelongsTo;

    /**
     * 标记为引入计算
     *
     * @return mixed
     */
    public function markAsIncluded();

    /**
     * 标记为不引入计算
     *
     * @return mixed
     */
    public function markAsUnIncluded();
}