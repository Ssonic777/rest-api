<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Group\GroupAdminResource;
use App\Services\GroupAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * class GroupAdminController
 * @package App\Http\Controllers
 */
class GroupAdminController extends Controller
{

    /**
     * @var GroupAdminService $service
     */
    private GroupAdminService $service;

    public function __construct(GroupAdminService $service)
    {
        $this->service = $service;
    }

    /**
     * @param int $groupId
     * @return JsonResponse
     */
    public function index(int $groupId): JsonResponse
    {
        $groupAdmins = $this->service->getAdmins($groupId);

        return response()->json($groupAdmins);
    }

    /**
     * @param Request $request
     * @param int $groupId
     * @return JsonResponse
     */
    public function store(Request $request, int $groupId): JsonResponse
    {
        $validated = $request->merge(['group_id' => $groupId])->validate([
            'group_id' => 'required|integer|exists:Wo_Groups,id',
            'user_id' => 'required|integer|exists:Wo_Users,user_id',
            'general' => 'boolean',
            'privacy' => 'boolean',
            'avatar' => 'boolean',
            'members' => 'boolean',
            'analytics' => 'boolean',
            'delete_group' => 'boolean'
        ]);

        $storesGroupAdmin = $this->service->storeGroupAdmin($validated['group_id'], (int)$validated['user_id'], $validated);

        return response()->json(GroupAdminResource::make($storesGroupAdmin));
    }

    /**
     * @param int $groupId
     * @param int $adminId
     * @return JsonResponse
     */
    public function show(int $groupId, int $adminId): JsonResponse
    {
        $showGroupAdmin = $this->service->showGroupAdmin($groupId, $adminId);

        return response()->json(GroupAdminResource::make($showGroupAdmin));
    }

    /**
     * @param Request $request
     * @param int $groupId
     * @param int $userId
     * @return JsonResponse
     */
    public function update(Request $request, int $groupId, int $userId): JsonResponse
    {
        $validated = $request->validate([
            'general' => 'boolean',
            'privacy' => 'boolean',
            'avatar' => 'boolean',
            'members' => 'boolean',
            'analytics' => 'boolean',
            'delete_group' => 'boolean'
        ]);

        $updatedGroupAdmin = $this->service->updateGroupAdmin($groupId, $userId, $validated);

        return response()->json(GroupAdminResource::make($updatedGroupAdmin));
    }

    /**
     * @param int $groupId
     * @param int $adminId
     * @return JsonResponse
     */
    public function destroy(int $groupId, int $adminId): JsonResponse
    {
        $result = $this->service->deleteGroupAdmin($groupId, $adminId);

        return response()->json($result, Response::HTTP_ACCEPTED);
    }
}
