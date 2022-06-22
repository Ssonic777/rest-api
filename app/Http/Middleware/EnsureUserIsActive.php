<?php

namespace App\Http\Middleware;

use App\Exceptions\ForbiddenPermissionException;
use Closure;
use Illuminate\Http\Request;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     * @throws ForbiddenPermissionException
     */
    public function handle(Request $request, Closure $next)
    {
        $current_user = auth()->user();

        if (!$current_user->active) {
            throw new ForbiddenPermissionException('Your account is not active.');
        }

        return $next($request);
    }
}
