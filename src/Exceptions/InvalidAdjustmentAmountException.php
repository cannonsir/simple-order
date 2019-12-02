<?php

namespace Gtd\SimpleOrder\Exceptions;

class InvalidAdjustmentAmountException extends \Exception
{
    protected $message = '调整金额无效';
}