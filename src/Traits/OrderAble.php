<?php

namespace Gtd\SimpleOrder\Traits;

// producible will use this trait
trait OrderAble
{
    public function getAmount()
    {
        throw new \RuntimeException('未设置getAmount方法');
    }
}