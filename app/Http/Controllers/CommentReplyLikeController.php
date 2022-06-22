<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CommentReplyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * class CommentReplyLikeController
 * @package App\Http\Controllers
 */
class CommentReplyLikeController extends Controller
{
    private ?User $authUser;
    private CommentReplyService $service;

    /**
     * @param CommentReplyService $service
     */
    public function __construct(CommentReplyService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @param int $commentId
     * @param int $replyId
     * @return JsonResponse
     */
    public function toggleLike(Request $request, int $commentId, int $replyId): JsonResponse
    {
        $validated = $request->merge([
            'comment_id' => $commentId,
            'reply_id' => $replyId,
        ])->validate([
            'comment_id' => 'required|exists:Wo_Comments,id',
            'reply_id' => 'required|exists:Wo_Comment_Replies,id',
        ]);

        $data = $this->service->toggleCommentReplyLike($this->authUser, $validated['reply_id']);

        return response()->json($data, JsonResponse::HTTP_ACCEPTED);
    }
}
