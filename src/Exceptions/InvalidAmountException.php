<?php

namespace Gtd\SimpleOrder\Exceptions;

class InvalidAmountException extends \Exception
{
    protected $message = '金额无效';
}