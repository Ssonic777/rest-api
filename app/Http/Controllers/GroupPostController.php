<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\User;
use App\Rules\FileExistsRule;
use App\Services\GroupPostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * class GroupPostController
 * @package App\Http\Controllers
 */
class GroupPostController extends Controller
{

    /**
     * @var User|null $authUser
     */
    private ?User $authUser;

    /**
     * @var GroupPostService $service
     */
    private GroupPostService $service;

    public function __construct(GroupPostService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
    }

    /**-
     * @param int $groupId
     * @return JsonResponse
     */
    public function index(int $groupId): JsonResponse
    {
        $groupPosts = $this->service->getGroupPosts($groupId);

        return response()->json(PostResource::collection($groupPosts));
    }

    /**
     * @param Request $request
     * @param int $groupId
     * @return JsonResponse
     */
    public function store(Request $request, int $groupId): JsonResponse
    {
        $validated = $request->merge(['group_id' => $groupId])->validate([
            'group_id' => 'required|exists:Wo_Groups,id',
            'post_text' => 'required|string',
            'post_privacy' => 'required|boolean',
            'attachments' => ['array', new FileExistsRule()]
        ]);

        $storedGroupPost = $this->service->storeGroupPost($this->authUser, $validated);

        return response()->json(PostResource::make($storedGroupPost));
    }

    /**
     * @param int $groupId
     * @param int $postId
     * @return JsonResponse
     */
    public function show(int $groupId, int $postId): JsonResponse
    {
        $showGroupPost = $this->service->showGroupPost($groupId, $postId);

        return response()->json(PostResource::make($showGroupPost));
    }

    /**
     * @param Request $request
     * @param int $groupId
     * @param int $postId
     * @return JsonResponse
     */
    public function update(Request $request, int $groupId, int $postId): JsonResponse
    {
        $validated = $request->validate([
            'post_text' => 'string',
            'active' => 'boolean',
            'post_privacy' => 'boolean',
            'attachments' => ['array', new FileExistsRule()],
        ]);

        $updatesGroupPost = $this->service->updateGroupPost($groupId, $postId, $validated);

        return response()->json(PostResource::make($updatesGroupPost));
    }

    /**
     * @param int $groupId
     * @param int $postId
     * @return Response
     */
    public function destroy(int $groupId, int $postId): Response
    {
        $this->service->deleteGroupPost($groupId, $postId);

        return response()->noContent();
    }
}
