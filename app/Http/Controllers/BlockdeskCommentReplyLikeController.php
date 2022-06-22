<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CommentReplyLikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * class BlockdeskCommentReplyLikeController
 * @package App\Http\Controllers
 */
class BlockdeskCommentReplyLikeController extends Controller
{
    /**
     * @var User|null $authUser
     */
    private ?User $authUser;

    /**
     * @var CommentReplyLikeService $service
     */
    private CommentReplyLikeService $service;

    public function __construct(CommentReplyLikeService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
    }

    /**
     * @param int $replyCommentId
     * @return JsonResponse
     */
    public function toggleLike(int $replyCommentId): JsonResponse
    {
        $result = $this->service->toggleLike($this->authUser, $replyCommentId);

        return response()->json($result, Response::HTTP_ACCEPTED);
    }
}
