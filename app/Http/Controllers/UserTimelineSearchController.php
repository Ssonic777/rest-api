<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserTimelineSearchService;
use App\Http\Resources\Collections\PostCollectionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * class UserTimelineSearchController
 * @package App\Http\Controllers
 */
class UserTimelineSearchController extends Controller
{
    private ?User $authUser;

    private UserTimelineSearchService $userTimelineSearchService;

    /**
     * @param UserTimelineSearchService $userTimelineSearchService
     */
    public function __construct(UserTimelineSearchService $userTimelineSearchService)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->userTimelineSearchService = $userTimelineSearchService;
    }

    /**
     * @param Request $request
     * @param int|null $userId
     * @return JsonResponse
     */
    public function search(Request $request, int $userId = null): JsonResponse
    {
        $request->merge(['user_id' => $userId])->validate([
            'user_id' => 'nullable|integer|exists:Wo_Users,user_id',
            'search' => 'string|required|min:3',
            'per_page' => 'integer|max:1000',
        ]);

        $userId ??= $this->authUser->user_id;
        $search = $request->query->get('search', '');
        $perPage = $request->query->getInt('per_page', 15);

        $foundPosts = $this->userTimelineSearchService->searchUserTimeline($this->authUser, $userId, $search, $perPage);

        return response()->json(PostCollectionResource::make($foundPosts));
    }
}
