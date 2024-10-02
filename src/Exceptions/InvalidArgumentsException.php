<?php

namespace Willow\Exceptions;

use Exception;
use Throwable;

class InvalidArgumentsException extends Exception
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        $this->message = trim("Invalid arguments used in anonymous function. $message");
    }
}
