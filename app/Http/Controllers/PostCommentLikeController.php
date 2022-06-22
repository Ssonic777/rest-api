<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * class PostCommentLikeController
 * @package App\Http\Controllers
 */
class PostCommentLikeController extends Controller
{
    private ?User $authUser;
    private CommentService $service;

    /**
     * @param CommentService $service
     */
    public function __construct(CommentService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @param int $postId
     * @param int $commentId
     * @return JsonResponse
     */
    public function toggleLike(Request $request, int $postId, int $commentId): JsonResponse
    {
        $validated = $request->merge([
            'post_id' => $postId,
            'comment_id' => $commentId,
        ])->validate([
            'post_id' => 'required|exists:Wo_Posts,post_id',
            'comment_id' => 'required|exists:Wo_Comments,id',
        ]);

        $data = $this->service->toggleCommentLike($this->authUser, $validated['comment_id']);

        return response()->json($data, JsonResponse::HTTP_ACCEPTED);
    }
}
