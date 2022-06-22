<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\BlogResource;
use App\Http\Resources\Collections\BlogCollectionResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\BlogService;
use Illuminate\Http\Response;

/**
 * class BlockdeskArticleController
 * @package App\Http\Controllers
 */
class BlockdeskArticleController extends Controller
{
    public ?User $authUser;

    public BlogService $service;

    /**
     * @param BlogService $service
     */
    public function __construct(BlogService $service)
    {
        $this->authUser = auth('api')->user();
        $this->service = $service;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        //
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        //
    }

    /**
     * @param int $articleId
     * @return JsonResponse
     */
    public function show(int $articleId): JsonResponse
    {
        if ($this->authUser instanceof User) {
            $userId = $this->authUser->user_id;
        } else {
            $userId = 0;
        }

        $article = $this->service->getArticle($articleId, $userId);

        return response()->json(BlogResource::make($article));
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        //
    }

    /**
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        //
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getMyArticles(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => 'integer|nullable|max:1000',
        ]);

        $articles = $this->service->getMyArticles($this->authUser, $request->query->getInt('per_page'));

        return response()->json(BlogCollectionResource::make($articles));
    }

    /**
     * @param int $articleId
     * @return JsonResponse
     */
    public function share(int $articleId): JsonResponse
    {
        $url = $this->service->getArticleUrl($articleId);

        return response()->json(['url' => $url]);
    }
}
