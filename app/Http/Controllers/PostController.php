<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Post\PostCreateRequest;
use App\Http\Resources\Collections\PostCollectionResource;
use App\Http\Resources\PostResource;
use App\Models\User;
use App\Rules\FileExistsRule;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;

/**
 * Class PostController
 * @package App\Http\Controllers
 */
class PostController extends Controller
{
    /**
     * @var User|\Illuminate\Contracts\Auth\Authenticatable|null $authUser
     */
    private ?User $authUser;

    /**
     * @var PostService $service
     */
    private PostService $service;

    /**
     * PostController constructor.
     * @param PostService $service
     */
    public function __construct(PostService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth('api')->user();
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $request->merge($request->query->all())->validate(['per_page' => 'nullable|integer|max:1000']);
        $perPage = $request->query->getInt('per_page');
        $posts = $this->service->getUserPosts($this->authUser, $perPage);

        return response()->json(PostCollectionResource::make($posts));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(PostCreateRequest $request): JsonResponse
    {
        $storedPost = $this->service->storePost($this->authUser, $request->validated());

        return response()->json(PostResource::make($storedPost));
    }

    /**
     * Display the specified resource.
     *
     * @param int $postId
     * @return JsonResponse
     */
    public function show(int $postId): JsonResponse
    {
        $post = $this->service->showPost($this->authUser, $postId);

        return response()->json(PostResource::make($post));
    }

    /**
     * @param Request $request
     * @param int $postId
     * @return JsonResponse
     */
    public function update(Request $request, int $postId): JsonResponse
    {
        $validated = $request->validate([
            'post_text' => 'required|string|max:5000',
            'active' => 'required|boolean',
            'post_privacy' => 'required|in:' . implode(',', Post::$privacyOptions),
            'comments_status' => 'required|boolean',
            'attachments.*' => 'required|string',
            'attachments' => ['array', new FileExistsRule(Post::ATTACHMENT_MIMETYPES)],
            'enable_notifications' => 'boolean',
            'service_gif' => 'nullable|string|url',
        ]);

        $updatedPost = $this->service->updatePost($this->authUser, $postId, $validated);

        return response()->json(PostResource::make($updatedPost));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $postId
     * @return Response
     */
    public function destroy(int $postId): Response
    {
        $this->service->deletePost($postId);

        return response()->noContent();
    }

    /**
     * @param int $postId
     * @return JsonResponse
     */
    public function share(int $postId): JsonResponse
    {
        $url = $this->service->getPostUrl($postId);

        return response()->json(compact('url'));
    }
}
