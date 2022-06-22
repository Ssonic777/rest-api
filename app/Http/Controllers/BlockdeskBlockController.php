<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\BlogResource;
use App\Http\Resources\Collections\BlogCollectionResource;
use App\Models\User;
use App\Services\BlogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * class BlockdeskBlockController
 * @package App\Http\Controllers
 */
class BlockdeskBlockController extends Controller
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
     * @param Request $request
     * @return JsonResponse
     */
    public function trendingBlock(Request $request): JsonResponse
    {
        $limit = $request->query->getInt('limit', 4);
        $trendingBlockArticles = $this->service->getTrendingBlockArticles($this->authUser, $limit);

        return response()->json(BlogResource::collection($trendingBlockArticles));
    }

    /**
     * @return JsonResponse
     */
    public function popularBlock(): JsonResponse
    {
        $popularBlockArticles = $this->service->getPopularBlockArticles($this->authUser);

        return response()->json(BlogResource::collection($popularBlockArticles));
    }

    /**
     * @return JsonResponse
     */
    public function editorsChoiceBlock(): JsonResponse
    {
        $editorsChoiceBlockArticles = $this->service->getEditorsChoiceBlockArticles($this->authUser);

        return response()->json(BlogResource::collection($editorsChoiceBlockArticles));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function latestBlock(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'categories_ids' => 'nullable|string',
            'per_page' => 'nullable|integer|max:1000',
        ]);

        $latestBlockArticles = $this->service->getLatestBlockArticles($this->authUser, $filters);

        return response()->json(BlogCollectionResource::make($latestBlockArticles));
    }
}
