<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\HotTodayTagPostService;
use App\Http\Resources\Collections\PostCollectionResource;
use App\Http\Resources\HashtagResource;

/**
 * class HotTodayTagPostController
 * @package App\Http\Controllers
 */
class HotTodayTagPostController extends Controller
{
    private ?User $authUser;

    private HotTodayTagPostService $service;

    /**
     * @param HotTodayTagPostService $service
     */
    public function __construct(HotTodayTagPostService $service)
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
        $hotTodayTags = $this->service->getHotTodayTags()->pluck('tag')->toArray();
        $hotTodayTags = implode(',', $hotTodayTags);

        $request->validate([
            'tag' => 'string|in:' . $hotTodayTags,
            'sorting' => 'string|in:popular,recent',
            'per_page' => 'integer|nullable|max:1000',
        ]);

        $tag = $request->query->get('tag');
        $sorting = $request->query->get('sorting', 'popular');
        $perPage = $request->query->getInt('per_page', 15);

        $hotTodayTagsPosts = $this->service->getHotTodayTagsPosts($this->authUser, $tag, $sorting, $perPage);

        return response()->json(PostCollectionResource::make($hotTodayTagsPosts));
    }

    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $hotTodayTags = $this->service->getHotTodayTags();

        return response()->json(HashtagResource::make($hotTodayTags));
    }
}
