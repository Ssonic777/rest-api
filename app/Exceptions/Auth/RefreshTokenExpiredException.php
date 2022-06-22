<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use Exception;
use Illuminate\Http\Response;

/**
 * class RefreshTokenExpiredException
 * @package App\Exception\Auth
 */
class RefreshTokenExpiredException extends Exception
{
    /**
     * @var string $message
     */
    protected $message = 'Refresh Token expired';

    /**
     * @var int $code
     */
    protected $code = Response::HTTP_PROXY_AUTHENTICATION_REQUIRED;
}
