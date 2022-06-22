<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Collections\CommentReplyCollectionResource;
use App\Http\Resources\CommentReplyResource;
use App\Models\Post;
use App\Models\User;
use App\Rules\FileExistsRule;
use App\Services\BlogCommentReplyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * class BlockdeskBlockCommentReplyController
 * @package App\Http\Controllers
 */
class BlockdeskBlockCommentReplyController extends Controller
{

    /**
     * @var User|null $authUser
     */
    private ?User $authUser;

    /**
     * @var BlogCommentReplyService $service
     */
    private BlogCommentReplyService $service;

    public function __construct(BlogCommentReplyService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @param int $blogId
     * @param int $blogCommentId
     * @return JsonResponse
     */
    public function index(Request $request, int $blogId, int $blogCommentId): JsonResponse
    {
        $perPage = $request->query->getInt('per_page');
        $commentReplies = $this->service->getCommentReplies($this->authUser, $blogId, $blogCommentId, $perPage);

        return response()->json(CommentReplyCollectionResource::make($commentReplies));
    }

    /**
     * @param Request $request
     * @param int $blogId
     * @param int $commentId
     * @return JsonResponse
     */
    public function store(Request $request, int $blogId, int $commentId): JsonResponse
    {
        $validated = $request->merge([
            'blog_id' => $blogId,
            'comment_id' => $commentId,
        ])
        ->validate([
            'blog_id' => 'required|integer|exists:Wo_Blog,id',
            'comment_id' => 'required|integer|exists:Wo_Comments,id',
            'text' => 'required_without:file|string',
            'file' => ['required_without:text', 'string', new FileExistsRule(Post::ATTACHMENT_MIMETYPES)],
        ]);

        $storedCommentReply = $this->service->storeCommentReply($this->authUser, $validated);

        return response()->json(CommentReplyResource::make($storedCommentReply));
    }

    /**
     * @param Request $request
     * @param int $blogId
     * @param int $commentId
     * @param int $commentReplyId
     * @return JsonResponse
     */
    public function show(Request $request, int $blogId, int $commentId, int $commentReplyId): JsonResponse
    {
        $request->merge([
            'blog_id' => $blogId,
            'comment_id' => $commentId,
            'comment_reply_id' => $commentReplyId,
        ])
        ->validate([
            'blog_id' => 'required|int|exists:Wo_Blog,id',
            'comment_id' => 'required|integer|exists:Wo_Comments,id',
            'comment_reply_id' => 'required|integer|exists:Wo_Comment_Replies,id'
        ]);

        $showCommentReply = $this->service->showCommentReply($this->authUser, $blogId, $commentId, $commentReplyId);

        return response()->json(CommentReplyResource::make($showCommentReply));
    }

    /**
     * @param Request $request
     * @param int $blogId
     * @param int $commentId
     * @param int $commentReplyId
     * @return JsonResponse
     */
    public function update(Request $request, int $blogId, int $commentId, int $commentReplyId): JsonResponse
    {
        $validated = $request->merge([
            'blog_id' => $blogId,
            'comment_id' => $commentId,
            'comment_reply_id' => $commentReplyId,
        ])
        ->validate([
            'blog_id' => 'required|int|exists:Wo_Blog,id',
            'comment_id' => 'required|integer|exists:Wo_Comments,id',
            'text' => 'nullable|required_without:file|string',
            'file' => ['nullable', 'required_without:text', 'string', new FileExistsRule(Post::ATTACHMENT_MIMETYPES)],
            'comment_reply_id' => 'required|integer|exists:Wo_Comment_Replies,id',
        ]);

        $updatedCommentReply = $this->service->updateCommentReply($this->authUser, $validated);

        return response()->json(CommentReplyResource::make($updatedCommentReply));
    }

    /**
     * @param Request $request
     * @param int $blogId
     * @param int $commentId
     * @param int $commentReplyId
     * @return Response
     */
    public function destroy(Request $request, int $blogId, int $commentId, int $commentReplyId): Response
    {
        $request->merge([
            'blog_id' => $blogId,
            'comment_id' => $commentId,
            'comment_reply_id' => $commentReplyId,
            ])
        ->validate([
            'blog_id' => 'required|int|exists:Wo_Blog,id',
            'comment_id' => 'required|integer|exists:Wo_Comments,id',
            'comment_reply_id' => 'required|integer|exists:Wo_Comment_Replies,id',
        ]);

        $this->service->deleteCommentReply($blogId, $commentId, $commentReplyId);

        return response()->noContent();
    }
}
