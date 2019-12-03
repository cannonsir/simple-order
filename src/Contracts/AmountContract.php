<?php


namespace Gtd\SimpleOrder\Contracts;


use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface AmountContract
{
    /**
     * 所属模型
     *
     * @return BelongsTo
     */
    public function belong(): BelongsTo;
}