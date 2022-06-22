<?php

declare(strict_types=1);

namespace App\Exceptions\Auth;

use Exception;
use Illuminate\Http\Response;

/**
 * class RefreshTokenNotFoundException
 * @package App\Exception\Auth
 */
class RefreshTokenNotFoundException extends Exception
{
    /**
     * @var string $message
     */
    protected $message = 'Refresh Token not found';

    /**
     * @var int $code
     */
    protected $code = Response::HTTP_PROXY_AUTHENTICATION_REQUIRED;
}
