<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\BlockdeskArticleFollowingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\Collections\BlogCollectionResource;

/**
 * Class BlockdeskArticleFollowingController
 * @package App\Http\Controllers
 */
class BlockdeskArticleFollowingController extends Controller
{
    /**
     * @var User|null $authUser
     */
    private ?User $authUser;

    /**
     * @var BlockdeskArticleFollowingService $service
     */
    private BlockdeskArticleFollowingService $service;

    public function __construct(BlockdeskArticleFollowingService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->query->getInt('per_page', 15);
        $articles = $this->service->getBlockdeskArticleFollowing($this->authUser, $perPage);

        return response()->json(BlogCollectionResource::make($articles));
    }
}
