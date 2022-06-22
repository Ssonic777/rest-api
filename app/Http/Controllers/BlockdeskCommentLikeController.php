<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BlockdeskCommentLikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * class BlockdeskCommentLikeController
 * @package App\Http\Controllers
 */
class BlockdeskCommentLikeController extends Controller
{

    /**
     * @var User|null $authUser
     */
    private ?User $authUser;

    /**
     * @var BlockdeskCommentLikeService $service
     */
    private BlockdeskCommentLikeService $service;

    public function __construct(BlockdeskCommentLikeService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
    }

    /**
     * @param int $articleId
     * @param int $commentId
     * @return JsonResponse
     */
    public function toggleLike(int $articleId, int $commentId): JsonResponse
    {
        $result = $this->service->toggleLike($this->authUser, $articleId, $commentId);

        return response()->json($result, Response::HTTP_ACCEPTED);
    }
}
