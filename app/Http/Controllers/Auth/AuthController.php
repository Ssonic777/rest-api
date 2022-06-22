<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\UserService;

/**
 * class AuthController
 * @package App\Http\Controllers\Auth
 */
class AuthController extends Controller
{
    use ApiResponseTrait;

    private UserService $userService;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->middleware('auth:api', ['except' => [
            'register',
            'login',
            'refresh'
        ]]);

        $this->userService = $userService;
    }

    /**
     * TODO: (akovalenko) refactor that method
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $fields = $request->validate([
            'first_name' => 'required|min:2|max:50|alpha',
            'last_name' => 'required|min:2|max:50|alpha',
            'email' => 'required|email|unique:Wo_Users,email',
            'password' => 'required|min:8|max:32|regex:' . User::PASSWORD_VALIDATION_REGEX,
        ]);

        $devCode = $this->userService->register($fields);

        return response()->json($devCode);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate(['device_id' => 'required|string']);
        $credentials = $request->only(['email', 'username', 'password']);
        $deviceId = $request->get('device_id');
        $ipAddress = $request->server->get('HTTP_X_ORIGINAL_FORWARDED_FOR', env('APP_ENV'));

        $data = $this->userService->login($credentials, $deviceId, $ipAddress);

        return response()->json($data);
    }

    /**
     * Get the authenticated User.
     * TODO: Move to auth trait
     * @return JsonResponse
     */
    public function user(): JsonResponse
    {
        $user = $this->userService->user();

        return response()->json(UserResource::make($user));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->validate(['device_id' => 'required|string']);
        $this->userService->logout($request->get('device_id'));

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \App\Exceptions\Auth\RefreshTokenExpiredException
     * @throws \App\Exceptions\Auth\RefreshTokenNotFoundException
     */
    public function refresh(Request $request): JsonResponse
    {
        $request->validate([
            'refresh_token' => 'required|string',
            'device_id' => 'required|string'
        ]);

        return response()->json($this->userService->refresh(
            $request->get('refresh_token'),
            $request->get('device_id')
        ));
    }
}
