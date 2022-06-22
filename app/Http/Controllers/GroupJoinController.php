<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\GroupMember;
use App\Models\User;
use App\Services\GroupJoinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * class GroupJoinController
 * @package App\Http\Controller
 */
class GroupJoinController extends Controller
{
    /**
     * @var User|null $authUser
     */
    private ?User $authUser;

    /**
     * @var GroupJoinService
     */
    private GroupJoinService $service;

    public function __construct(GroupJoinService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
    }

    /**
     * @param int $groupId
     * @return JsonResponse
     */
    public function joinToggle(int $groupId): JsonResponse
    {
        $result = $this->service->joinToggle($this->authUser, $groupId);

        return response()->json($result);
    }

    /**
     * @param Request $request
     * @param int $groupId
     * @return JsonResponse
     */
    public function getRequests(Request $request, int $groupId): JsonResponse
    {
        $request->validate(['per_page' => 'nullable|integer|max:1000']);
        $perPage = $request->query->getInt('per_page');
        $requests = $this->service->requests($groupId, $perPage);

        return response()->json($requests);
    }

    /**
     * @param Request $request
     * @param int $groupId
     * @param int $userId
     * @return JsonResponse
     */
    public function requestAction(Request $request, int $groupId, int $userId): JsonResponse
    {
        $validated = $request->validate(['request' => 'required|string|in:' . implode(',', GroupMember::REQUEST_STATUSES)]);

        $result = $this->service->requestAnswer($groupId, $userId, $validated);

        return response()->json($result);
    }
}
