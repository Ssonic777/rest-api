<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Filters\GroupFilter;
use App\Http\Resources\Collections\GroupCollectionResource;
use App\Http\Resources\GroupResource;
use App\Models\User;
use App\Services\GroupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * class GroupController
 * @package App\Http\Controllers
 */
class GroupController extends Controller
{

    /**
     * @var User|null $authUser
     */
    private ?User $authUser;

    /**
     * @var GroupService $service
     */
    private GroupService $service;

    public function __construct(GroupService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'group_title' => 'required|string|max:255',
            'group_slug' => 'required|string|alpha_dash|max:255|unique:Wo_Groups,group_name',
            'category' => 'required|integer|exists:Wo_Groups_Categories,id',
            'about' => 'nullable|string',
            'privacy' => 'boolean'
        ]);

        $storedGroup = $this->service->storeGroup($this->authUser, $validated);

        return response()->json(GroupResource::make($storedGroup));
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $showGroup = $this->service->showGroup($this->authUser, $id);

        return response()->json(GroupResource::make($showGroup));
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'group_title' => 'string|max:255',
            'group_slug' => "string|alpha_dash|max:255|unique:Wo_Groups,group_name,{$id},id",
            'category' => 'integer|exists:Wo_Groups_Categories,id',
            'about' => 'nullable|string',
            'privacy' => 'boolean',
            'join_privacy' => 'boolean'
        ]);

        $updatedGroup = $this->service->updateGroup($id, $validated);

        return response()->json(GroupResource::make($updatedGroup));
    }

    /**
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $this->service->deleteGroup($id);

        return response()->noContent();
    }
}
