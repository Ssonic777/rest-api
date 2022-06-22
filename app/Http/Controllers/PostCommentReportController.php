<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;

/**
 * class PostCommentReportController
 * @package App\Http\Controllers
 */
class PostCommentReportController extends Controller
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
    public function reportComment(Request $request, int $postId, int $commentId): JsonResponse
    {
        $validated = $request->merge([
            'post_id' => $postId,
            'comment_id' => $commentId,
        ])->validate([
            'post_id' => 'required|integer|exists:Wo_Posts,post_id',
            'comment_id' => 'required|integer|exists:Wo_Comments,id',
            'text' => 'required|string|min:4|max:40',
        ]);

        $comment = $this->service->showComment($this->authUser, $validated['post_id'], $validated['comment_id']);
        $data = $this->service->reportComment($this->authUser, $comment, $validated['text']);

        return response()->json($data, JsonResponse::HTTP_ACCEPTED);
    }

    /**
     * @param Request $request
     * @param int $postId
     * @param int $commentId
     * @return JsonResponse
     */
    public function withdrawCommentReport(Request $request, int $postId, int $commentId): JsonResponse
    {
        $validated = $request->merge([
            'post_id' => $postId,
            'comment_id' => $commentId,
        ])->validate([
            'post_id' => 'integer|required|exists:Wo_Posts,post_id',
            'comment_id' => 'integer|required|exists:Wo_Comments,id',
        ]);

        $comment = $this->service->showComment($this->authUser, $validated['post_id'], $validated['comment_id']);
        $data = $this->service->withdrawCommentReport($this->authUser, $comment);

        return response()->json($data, JsonResponse::HTTP_ACCEPTED);
    }
}
