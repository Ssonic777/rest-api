<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Exceptions\ForbiddenPermissionException;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/**
 * class ActiveController
 * @package App\Http\Controllers\Auth
 */
class ActiveController extends Controller
{

    /**
     * @var UserService $userService
     */
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->middleware('guest');
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \App\Exceptions\UserAlreadyActiveException
     */
    public function generateActiveToken(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        $devCode = $this->userService->generateActiveToken($request->get('email'));

        return response()->json($devCode, Response::HTTP_ACCEPTED);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ForbiddenPermissionException|ValidationException
     */
    public function userActive(Request $request): JsonResponse
    {
        $fields = $request->validate([
            'otp_code' => 'required',
            'email' => 'required|email',
            'device_id' => 'required|string'
        ]);

        $deviceId = $request->get('device_id');
        $ipAddress = $request->server->get('HTTP_X_ORIGINAL_FORWARDED_FOR', env('APP_ENV'));

        $authToken = $this->userService->submitActive($fields, $deviceId, $ipAddress);

        // TODO: Move to auth trait
        return response()->json($authToken);
    }
}
