<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * class ClientHeaderInfoMiddleware
 * @package App\Http\Middleware
 */
class ClientHeaderInfoMiddleware
{

    private array $supportIOS = [
        'os' => 'IOS',
        'os-version' => null,
        'app-version' => null,
        'device-id' => 'iosDeviceId',
    ];

    private array $supportAndroid = [
        'os' => 'Android',
        'os-version' => null,
        'app-version' => null,
        'device-id' => 'androidDeviceId',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {



        return $next($request);
    }
}
