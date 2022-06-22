<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserChangePasswordService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * class ChangePasswordController
 * @package App\Http\Controllers\Auth
 */
class ChangePasswordController extends Controller
{
    public ?User $authUser;

    public UserChangePasswordService $service;

    /**
     * @param UserChangePasswordService $service
     */
    public function __construct(UserChangePasswordService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth('api')->user();
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @param array $rules
     * @return array
     */
    public function validateRequest(Request $request, array $rules = []): array
    {
        $defaultRules = [
            'current_password' => 'required|password:api',
            'new_password' => 'required|min:8|max:32|regex:' . User::PASSWORD_VALIDATION_REGEX,
        ];

        return $request->validate(array_merge($defaultRules, $rules));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function requestPasswordChange(Request $request): JsonResponse
    {
        $this->validateRequest($request);
        $this->service->requestPasswordChange($this->authUser);

        return response()->json(['message' => 'Verification code sent! Please verify your request.']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyPasswordChange(Request $request): JsonResponse
    {
        $validated = $this->validateRequest($request, ['email_code' => 'required|digits:6']);

        $validated['password'] = $validated['new_password'];
        unset($validated['current_password']);
        unset($validated['new_password']);

        $this->service->verifyPasswordChange($this->authUser, $validated);

        return response()->json(['message' => 'Password successfully changed.']);
    }
}
