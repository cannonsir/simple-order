<?php

namespace Gtd\SimpleOrder\Contracts;

use Illuminate\Database\Eloquent\Relations\HasOne;

interface HasAmountContract
{
    /**
     * 获取金额实例
     *
     * @return HasOne
     */
    public function amount(): HasOne;

    /**
     * 获取调整金额总计
     *
     * @return string 金额
     */
    public function getAdjustmentsTotal(): string;

    /**
     * 设置调整金额总计
     *
     * @param integer|string $adjustments_amount_total 金额
     * @return mixed
     */
    public function setAdjustmentsTotal($adjustments_amount_total);

    /**
     * 获取原始金额
     *
     * @return string
     */
    public function getOriginAmount(): string;

    /**
     * 设置原始金额
     *
     * @param $should_amount
     * @return mixed
     */
    public function setOriginAmount($should_amount);

    /**
     * 获取最终金额
     *
     * @return string
     */
    public function getResAmount(): string;

    /**
     * 设置最终金额
     *
     * @param integer|string $res_amount
     * @return mixed
     */
    public function setResAmount($res_amount);

    /**
     * 计算调整金额总量
     *
     * @return string
     */
    public function calculateAdjustmentsTotal(): string;

    /**
     * 计算最终金额
     *
     * @return string
     */
    public function calculateResAmount(): string;
}