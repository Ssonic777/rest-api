<?php

namespace App\Http\Middleware;

use App\Exceptions\ForbiddenPermissionException;
use Closure;
use Illuminate\Http\Request;

class EnsureUserModificationPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $userID = $user->getAuthIdentifier();
        $requestedUserID = $request->user_id;

        if (isset($userID) && isset($requestedUserID) && $userID != $requestedUserID) {
            throw new ForbiddenPermissionException();
        }

        if ($user->active != 1) {
            throw new ForbiddenPermissionException("Current user is not active");
        }

        return $next($request);
    }
}
