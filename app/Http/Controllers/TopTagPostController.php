<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TopTagPostService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Collections\PostCollectionResource;
use App\Http\Resources\HashtagResource;

/**
 * class TopTagPostController
 * @package App\Http\Controllers
 */
class TopTagPostController extends Controller
{
    private ?User $authUser;

    private TopTagPostService $service;

    /**
     * @param TopTagPostService $service
     */
    public function __construct(TopTagPostService $service)
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
        $topTags = $this->service->getTopTags();
        $topTags = implode(',', $topTags);

        $request->validate([
            'tag' => 'string|in:' . $topTags,
            'sorting' => 'string|in:popular,recent',
            'per_page' => 'integer|nullable|max:1000',
        ]);

        $tag = $request->query->get('tag');
        $sorting = $request->query->get('sorting', 'popular');
        $perPage = $request->query->getInt('per_page', 15);

        $topTagsPosts = $this->service->getTopTagsPosts($this->authUser, $tag, $sorting, $perPage);

        return response()->json(PostCollectionResource::make($topTagsPosts));
    }

    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $topTags = $this->service->getTopTags();

        return response()->json($topTags);
    }
}
