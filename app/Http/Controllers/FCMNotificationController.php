<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * class FCMNotification
 * @package App\Http\Controllers
 */
class FCMNotificationController extends Controller
{

    /**
     * @var User|null $authUser
     */
    private ?User $authUser;

    /**
     * @var NotificationService $service
     */
    private NotificationService $service;

    public function __construct(NotificationService $service)
    {
        // $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @param int|null $userId
     * @return JsonResponse
     */
    public function send(Request $request, int $userId = null): JsonResponse
    {
        $validated = $request->merge(['user_id' => $userId])->validate(
            [
                'user_id' => 'nullable|integer|exists:Wo_Users,user_id|exists:fcm_tokens,user_id',
                'title' => 'string|max:50',
                'subtitle' => 'string|max:50',
                'body' => 'string|max:80',
                'type' => 'nullable|string|alpha_dash',
                'key' => 'string|max:80'
            ],
            [
                'user_id.exists' => "User by id: {$userId} not found or not found user 'device_token'"
            ]
        );

        $userId ??= $this->authUser->user_id;
        $res = $this->service->notifier($userId, $request->all());

        return response()->json($res);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveDeviceToken(Request $request): JsonResponse
    {
        $validated = $request->merge(['user_agent' => $request->userAgent()])->validate([
            'user_agent' => 'required|string',
            'device_token' => 'required|string',
            'device_id' => 'required|string|exists:auth_refresh_tokens,device_id',
        ]);

        $result = $this->service->saveDeviceToken($this->authUser, $validated);

        return response()->json($result, Response::HTTP_ACCEPTED);
    }
}
