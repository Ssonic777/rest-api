<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserNotificationsSettingsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * class UserNotificationsSettingsController
 * @package App\Http\Controllers
 */
class UserNotificationsSettingsController extends Controller
{
    private ?User $authUser;

    private UserNotificationsSettingsService $service;

    /**
     * @param UserNotificationsSettingsService $service
     */
    public function __construct(UserNotificationsSettingsService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth('api')->user();
        $this->service = $service;
    }

    /**
     * @return JsonResponse
     */
    public function getUserNotificationsSettings(): JsonResponse
    {
        $settings = $this->service->getUserNotificationsSettings($this->authUser->user_id);

        return response()->json($settings);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function setUserNotificationsSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'allow' => 'required|string|in:' . implode(',', array_keys(User::NOTIFICATIONS_ALLOW)),
            'from' => 'required|string|in:' . implode(',', array_keys(User::NOTIFICATIONS_ALLOW_FROM)),
            'sounds' => 'required|string|in:' . implode(',', array_keys(User::NOTIFICATIONS_SOUNDS)),
        ]);

        $settings = $this->service->setUserNotificationsSettings($this->authUser->user_id, $validated);

        return response()->json($settings);
    }
}
