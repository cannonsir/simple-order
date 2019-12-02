<?php

namespace Gtd\SimpleOrder\Exceptions;

class OrderLockedException extends \Exception
{
    protected $message = '订单已锁定，不可修改';
}