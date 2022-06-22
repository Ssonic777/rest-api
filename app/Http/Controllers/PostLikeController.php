<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\ConfigService;

/**
 * class PostLikeController
 * @package App\Http\Controller
 */
class PostLikeController extends Controller
{
    /**
    * @var User|null $authUser
    */
    private ?User $authUser;

    /**
    * @var PostService $postService
    */
    private PostService $postService;
    /**
    * @var ConfigService $configService
    */
    private ConfigService $configService;

    public function __construct(PostService $postService, ConfigService $configService)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->postService = $postService;
        $this->configService = $configService;
    }

    /**
     * @param Request $request
     * @param int $postId
     * @return JsonResponse
     */
    public function likeToggle(Request $request, int $postId): JsonResponse
    {
        $validated = $request->merge([
            'post_id' => $postId,
        ])->validate([
            'post_id' => 'required|exists:Wo_Posts,id',
        ]);
        $status = $this->postService->likePostToggle($this->authUser, $validated['post_id']);

        return response()->json($status, JsonResponse::HTTP_ACCEPTED);
    }

    /**
     * @param int $postId
     * @return JsonResponse
     */
    public function getLikedUsers(int $postId): JsonResponse
    {
        $likedUsers = $this->postService->getLikedUsers($postId);

        return response()->json(UserResource::collection($likedUsers));
    }
}
