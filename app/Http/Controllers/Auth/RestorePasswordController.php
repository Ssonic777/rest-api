<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/**
 * class RestorePasswordController
 * @package App\Http\Controllers\Auth
 */
class RestorePasswordController extends Controller
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
     * @throw ValidationException
     */
    public function generateResetPassword(Request $request): JsonResponse
    {
        $fields = $request->validate([
            'email' => 'required|email',
        ]);

        $devCode = $this->userService->generateResetPassword($fields);

        return response()->json($devCode, Response::HTTP_ACCEPTED);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function storeNewPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email_code' => 'required|exists:Wo_Users,email_code',
            'password' => 'required|min:6',
            'device_id' => 'required|string'
        ]);

        $deviceId = $request->get('device_id');
        $ipAddress = $request->server->get('HTTP_X_ORIGINAL_FORWARDED_FOR', env('APP_ENV'));

        $response = $this->userService->storeNewPassword(
            $request->only(['email_code', 'password']),
            $deviceId,
            $ipAddress
        );

        return response()->json($response);
    }
}
