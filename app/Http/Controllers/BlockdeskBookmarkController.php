<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Collections\BlogCollectionResource;
use App\Models\User;
use App\Services\BlogBookmarkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * class BlockdeskBookmarkController
 * @package App\Http\Controllers
 */
class BlockdeskBookmarkController extends Controller
{
    /**
     * @var User|null $authUser
     */
    private ?User $authUser;

    /**
     * @var BlogBookmarkService $service
     */
    private BlogBookmarkService $service;

    public function __construct(BlogBookmarkService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getBookmarks(Request $request): JsonResponse
    {
        $perPage = $request->request->getInt('per_page');
        $bookmarks = $this->service->getBookmarks($this->authUser, $perPage);

        return response()->json(BlogCollectionResource::make($bookmarks));
    }

    /**
     * @param int $blogId
     * @return JsonResponse
     */
    public function toggleBookmark(int $blogId): JsonResponse
    {
        $saved = $this->service->toggle($this->authUser, $blogId);

        return response()->json(compact('saved'));
    }
}
