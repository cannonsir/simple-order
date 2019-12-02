<?php

namespace Gtd\SimpleOrder\Exceptions;

class OrderItemCannotUpdateException extends \Exception
{
    protected $message = '订单子项目不可修改';
}