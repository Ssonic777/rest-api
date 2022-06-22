<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ForbiddenPermissionException extends Exception
{
    public function __construct($message = "Forbidden resource", $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
