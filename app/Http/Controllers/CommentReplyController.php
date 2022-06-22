<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Rules\FileExistsRule;
use App\Services\CommentReplyService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CommentReplyResource;
use App\Http\Resources\Collections\CommentReplyCollectionResource;

/**
 * class CommentReplyController
 * @package App\Http\Controllers
 */
class CommentReplyController extends Controller
{
    private ?User $authUser;
    private CommentReplyService $service;

    /**
     * @param CommentReplyService $service
     */
    public function __construct(CommentReplyService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth('api')->user();
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param int $commentId
     * @return JsonResponse
     */
    public function index(int $commentId): JsonResponse
    {
        $replies = $this->service->getCommentReplies($commentId);

        return response()->json(CommentReplyResource::collection($replies));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param int $commentId
     * @return JsonResponse
     */
    public function store(Request $request, int $commentId): JsonResponse
    {
        $fields = $request->merge(['comment_id' => $commentId])->validate([
            'comment_id' => 'required|integer|exists:Wo_Comments,id',
            'text' => 'required_without:file|string',
            'file' => ['required_without:text', 'string', new FileExistsRule(Post::ATTACHMENT_MIMETYPES)],
        ]);

        $reply = $this->service->storeCommentReply($this->authUser, $fields);

        return response()->json(CommentReplyResource::make($reply));
    }

    /**
     * Display the specified resource.
     *
     * @param int $commentId
     * @param int $replyId
     * @return JsonResponse
     */
    public function show(int $commentId, int $replyId): JsonResponse
    {
        $reply = $this->service->showCommentReply($commentId, $replyId);

        return response()->json(CommentReplyResource::make($reply));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $commentId
     * @param int $replyId
     * @return JsonResponse
     */
    public function update(Request $request, int $commentId, int $replyId): JsonResponse
    {
        $fields = $request->merge(['comment_id' => $commentId])->validate([
            'comment_id' => 'required|integer|exists:Wo_Comments,id',
            'text' => 'nullable|required_without:file|string',
            'file' => ['nullable', 'required_without:text', 'string', new FileExistsRule(Post::ATTACHMENT_MIMETYPES)],
        ]);

        $reply = $this->service->updateCommentReply($commentId, $replyId, $fields);

        return response()->json(CommentReplyResource::make($reply));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $commentId
     * @param int $replyId
     * @return Response
     */
    public function destroy(int $commentId, int $replyId): Response
    {
        $this->service->deleteCommentReply($commentId, $replyId);

        return response()->noContent(Response::HTTP_OK);
    }
}
