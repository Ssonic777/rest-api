<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\BlogCommentResource;
use App\Http\Resources\Collections\BlogCommentCollectionResource;
use App\Models\Post;
use App\Rules\FileExistsRule;
use App\Models\User;
use App\Services\BlogCommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * class BlockdeskCommentController
 * @package App\Http\Controllers
 */
class BlockdeskBlockCommentController extends Controller
{

    /**
     * @var User|null $authUser
     */
    private ?User $authUser;

    /**
     * @var BlogCommentService $service
     */
    private BlogCommentService $service;

    public function __construct(BlogCommentService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @param int $blogId
     * @return JsonResponse
     */
    public function index(Request $request, int $blogId): JsonResponse
    {
        $comments = $this->service->getComments($this->authUser, $blogId, $request->query->getInt('per_page'));

        return response()->json(BlogCommentCollectionResource::make($comments));
    }

    /**
     * @param Request $request
     * @param int $blogId
     * @return JsonResponse
     */
    public function store(Request $request, int $blogId): JsonResponse
    {
        $validated = $request->merge(['article_id' => $blogId])->validate([
            'article_id' => 'required|int|exists:Wo_Blog,id',
            'text' => 'required_without:file|string',
            'file' => ['required_without:text', 'string', new FileExistsRule(Post::ATTACHMENT_MIMETYPES)],
        ]);

        $createdBlogComment = $this->service->createBlogComment($this->authUser, $validated);

        return response()->json(BlogCommentResource::make($createdBlogComment));
    }

    /**
     * @param int $blogId
     * @param int $commentId
     * @return JsonResponse
     */
    public function show(int $blogId, int $commentId): JsonResponse
    {
        $showBlogComment = $this->service->showBlogComment($this->authUser, $blogId, $commentId);

        return response()->json(BlogCommentResource::make($showBlogComment));
    }

    /**
     * @param Request $request
     * @param int $blogId
     * @param int $commentId
     * @return JsonResponse
     */
    public function update(Request $request, int $blogId, int $commentId): JsonResponse
    {
        $validated = $request->merge([
            'article_id' => $blogId,
            'comment_id' => $commentId
        ])->validate([
            'article_id' => 'required|integer|exists:Wo_Blog,id',
            'comment_id' => 'required|integer|exists:Wo_Comments,id',
            'text' => 'nullable|required_without:file|string',
            'file' => ['nullable', 'required_without:text', 'string', new FileExistsRule(Post::ATTACHMENT_MIMETYPES)],
        ]);

        $updatedGroupComment = $this->service->updateBlogComment($this->authUser, $validated);

        return response()->json(BlogCommentResource::make($updatedGroupComment));
    }

    /**
     * @param int $blogId
     * @param int $commentId
     * @return Response
     */
    public function destroy(int $blogId, int $commentId): Response
    {
        $this->service->deleteBlogComment($blogId, $commentId);

        return response()->noContent();
    }
}
