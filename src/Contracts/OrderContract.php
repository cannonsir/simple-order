<?php

namespace Gtd\SimpleOrder\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface OrderContract extends HasAdjustmentsContract, HasAmountContract
{
    /**
     * @return MorphTo 所属用户
     */
    public function user(): MorphTo;

    /**
     * @return HasMany 项目列表
     */
    public function items(): HasMany;

    /**
     * 通过订单号查找订单
     *
     * @param string $number
     * @return OrderContract
     */
    public static function findByNumber(string $number): self;

    /**
     * 生成订单编号
     *
     * @return mixed
     */
    public static function generateNumber();

    /**
     * 新增商品项目
     *
     * @param $orderable array|Model 商品数据
     * @param $orderable_unit_price string|integer 商品单价
     * @param int $quantity 商品数量
     * @return false|Model
     */
    public function addItem($orderable, $orderable_unit_price = 0, int $quantity = 1);

    /**
     * 计算订单金额
     */
    public function calculator();
}