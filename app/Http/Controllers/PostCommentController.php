<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Collections\PostCommentCollectionResource;
use App\Http\Resources\PostCommentResource;
use App\Models\Post;
use App\Rules\FileExistsRule;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Http\Response;

/**
 * class PostCommentController
 * @package App\Http\Controllers
 */
class PostCommentController extends Controller
{

    private ?User $authUser;
    private CommentService $service;

    public function __construct(CommentService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth('api')->user();
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @param int $postId
     * @return JsonResponse
     */
    public function index(Request $request, int $postId): JsonResponse
    {
        $request->merge($request->query->all())->validate([
            'sort_by' => 'nullable|string|in:id,post_id,text,time,likes_count,replies_count',
            'sort_rule' => 'nullable|string|in:ASC,DESC'
        ]);

        $sortBy = $request->query('sort_by', 'id');
        $sortDirection = $request->query('sort_rule', 'ASC');
        $perPage = $request->query->getInt('per_page');

        $postComments = $this->service->getPostComments($this->authUser, $postId, $sortBy, $sortDirection, $perPage);

        return response()->json(PostCommentCollectionResource::make($postComments));
    }

    /**
     * @param Request $request
     * @param int $postId
     * @return JsonResponse
     */
    public function store(Request $request, int $postId): JsonResponse
    {
        $fields = $request->merge(['post_id' => $postId])->validate([
            'post_id' => 'nullable|integer|exists:Wo_Posts,post_id',
            'page_id' => 'nullable|integer|exists:Wo_Pages,page_id',
            'text' => 'required_without:file|string',
            'file' => ['required_without:text', 'string', new FileExistsRule(Post::ATTACHMENT_MIMETYPES)],
        ]);

        $storedComment = $this->service->storeComment($this->authUser, $fields);

        return response()->json(PostCommentResource::make($storedComment));
    }

    /**
     * @param int $postId
     * @param int $commentId
     * @return JsonResponse
     */
    public function show(int $postId, int $commentId): JsonResponse
    {
        $showComment = $this->service->showComment($this->authUser, $postId, $commentId);

        return response()->json(PostCommentResource::make($showComment));
    }

    /**
     * @param Request $request
     * @param int $postId
     * @param int $commentId
     * @return JsonResponse
     */
    public function update(Request $request, int $postId, int $commentId): JsonResponse
    {
        $fields = $request->merge(['post_id' => $postId])->validate([
            'post_id' => 'required|integer|exists:Wo_Posts,post_id',
            'text' => 'nullable|required_without:file|string',
            'file' => ['nullable', 'required_without:text', 'string', new FileExistsRule(Post::ATTACHMENT_MIMETYPES)],
        ]);

        $updatedComment = $this->service->updateComment($this->authUser, $postId, $commentId, $fields);

        return response()->json(PostCommentResource::make($updatedComment), Response::HTTP_ACCEPTED);
    }

    /**
     * @param int $postId
     * @param int $commentId
     * @return Response
     */
    public function destroy(int $postId, int $commentId): Response
    {
        $this->service->deleteComment($postId, $commentId);

        return response()->noContent();
    }
}
