<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class UserAlreadyActiveException extends Exception
{
    public function __construct($message = "User is already active", $code = 202, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
