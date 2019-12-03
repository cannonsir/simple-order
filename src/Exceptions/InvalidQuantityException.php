<?php

namespace Gtd\SimpleOrder\Exceptions;

class InvalidQuantityException extends \Exception
{
    protected $message = '项目单位数量无效';
}