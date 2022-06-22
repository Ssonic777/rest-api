<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Filters\GroupFilter;
use App\Http\Resources\Collections\GroupCollectionResource;
use App\Models\User;
use App\Services\GroupActionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * class GroupActionController
 * @package App\Http\Controllers
 */
class GroupActionController extends Controller
{
    /**
     * @var User|null $authUser
     */
    private ?User $authUser;

    /** @var GroupActionService $service */
    private GroupActionService $service;

    public function __construct(GroupActionService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $request->merge($request->query->all())->validate([
            'tab' => 'nullable|string|in:' . implode(',', GroupActionService::GROUP_TABS),
            'per_page' => 'nullable|integer|max:1000'
        ]);

        $perPage = $request->query->getInt('per_page');
        $tab = $request->query->getAlpha('tab');
        $groups = $this->service->getTabGroups($this->authUser, $tab, $perPage);

        return response()->json(GroupCollectionResource::make($groups));
    }

    /**
     * @param Request $request
     * @param int|null $userId
     * @return JsonResponse
     */
    public function userCreatedGroups(Request $request, int $userId = null): JsonResponse
    {
        $request->merge($request->query->all())->validate(['per_page' => 'nullable|integer|max:1000']);
        $userId ??= $this->authUser->user_id;
        $perPage = $request->query->getInt('per_page');
        $userCreatedGroups = $this->service->userCreatedGroups($userId, $perPage);

        return response()->json(GroupCollectionResource::make($userCreatedGroups));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function suggestions(Request $request): JsonResponse
    {
        $request->merge($request->query->all())->validate(['per_page' => 'nullable|integer|max:1000']);
        $perPage = $request->query->getInt('per_page');
        $suggestsGroups = $this->service->suggestions($this->authUser, $perPage);

        return response()->json(GroupCollectionResource::make($suggestsGroups));
    }

    /**
     * @param Request $request
     * @param int|null $userId
     * @return JsonResponse
     */
    public function joined(Request $request, int $userId = null): JsonResponse
    {
        $request->validate([
            'joined_type' => 'nullable|string|in:joined,requested',
            'per_page' => 'nullable|integer'
        ]);

        $userId ??= $this->authUser->user_id;
        $jointedType = $request->query('joined_type');
        $perPage = $request->query->getInt('per_page');

        $userGroups = $this->service->userJoinedGroups($userId, $jointedType, $perPage);

        return response()->json(GroupCollectionResource::make($userGroups));
    }

    /**
     * @param Request $request
     * @param int $groupId
     * @return JsonResponse
     */
    public function members(Request $request, int $groupId): JsonResponse
    {
        $request->merge($request->query->all())->validate(['per_page' => 'nullable|integer|max:1000']);
        $joinedType = $request->query('joined_type');
        $perPage = $request->query->getInt('per_page');
        $groupMembers = $this->service->groupMembers($groupId, $joinedType, $perPage);

        return response()->json($groupMembers);
    }

    /**
     * @param Request $request
     * @param int $groupId
     * @return Response
     */
    public function invite(Request $request, int $groupId): Response
    {
        $validated = $request->merge(['group_id' => $groupId])->validate([
            'group_id' => 'required|integer|exists:Wo_Groups,id',
            'friends' => 'required|array',
            'friends.*' => 'required|integer|exists:Wo_Users,user_id'
        ]);

        $this->service->invite($this->authUser, $groupId, $validated);

        return response()->noContent(Response::HTTP_ACCEPTED);
    }

    /**
     * @param GroupFilter $filter
     * @return JsonResponse
     */
    public function search(GroupFilter $filter): JsonResponse
    {
        return response()->json(GroupCollectionResource::make(
            $this->service->search($this->authUser, $filter)
        ));
    }
}
