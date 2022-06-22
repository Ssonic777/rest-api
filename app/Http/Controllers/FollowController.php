<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\FollowService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\Collections\UserCollectionResource;
use App\Http\Resources\Collections\FollowerCollectionResource;
use App\Http\Resources\Collections\FollowingCollectionResource;
use App\Http\Resources\Collections\FollowingRequestCollectionResource;

/**
 * class FollowController
 * @package App\Http\Controllers
 */
class FollowController extends Controller
{
    /**
     * @var FollowService $service
     */
    private FollowService $service;

    /**
    * @var User|null $authUser
    */
    private ?User $authUser;

    public function __construct(FollowService $service, UserService $userService)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $users = $this->service->getUsersToFollowPaginate($this->authUser);

        return response()->json($users);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function popular(Request $request): JsonResponse
    {
        $popularUsers = $this->service->popularUsersToFollow($this->authUser);

        return response()->json(UserResource::collection($popularUsers));
    }

    /**
     * @param int $userId
     * @return JsonResponse
     */
    public function followToggle(int $userId): JsonResponse
    {
        $strStatus = $this->service->followToggle($this->authUser, $userId);

        return response()->json(['message' => $strStatus]);
    }

    /**
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function getFollowers(Request $request, int $userId): JsonResponse
    {
        $request->merge(array_merge($request->query->all(), ['user_id' => $userId]))->validate([
            'user_id' => 'nullable|integer|exists:Wo_Users,user_id',
            'per_page' => 'nullable|integer|max:1000'
        ]);

        $perPage = $request->query->getInt('per_page', 15);

        $followers = $this->service->getFollowers($this->authUser, $userId, $perPage);

        return response()->json(FollowerCollectionResource::make($followers));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserFollowers(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => 'nullable|integer|max:1000'
        ]);

        $perPage = $request->query->getInt('per_page', 15);

        $followers = $this->service->getUserFollowers($this->authUser->user_id, $perPage);

        return response()->json(FollowerCollectionResource::make($followers));
    }

    /**
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function getFollowersSearch(Request $request, int $userId): JsonResponse
    {
        $request->merge(['user_id' => $userId])->validate([
            'user_id' => 'required|integer|exists:Wo_Users,user_id',
            'search' => 'required|string|min:3,max:50|regex:/^[a-zA-Z0-9_ ]+$/',
            'per_page' => 'nullable|integer|max:1000'
        ]);

        $perPage = $request->query->getInt('per_page', 15);

        $search = $request->query->get('search');

        $followers = $this->service->getFollowersSearch($userId, $perPage, $search);

        return response()->json(UserCollectionResource::make($followers));
    }

    /**
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function getFollowings(Request $request, int $userId): JsonResponse
    {
        $request->merge(array_merge($request->query->all(), ['user_id' => $userId]))->validate([
            'user_id' => 'nullable|integer|exists:Wo_Users,user_id',
            'per_page' => 'nullable|integer|max:1000'
        ]);

        $perPage = $request->query->getInt('per_page', 15);

        /** @var User $foundUser */
        $followings = $this->service->getFollowings($this->authUser->user_id, $userId, $perPage);

        return  response()->json(FollowingCollectionResource::make($followings));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserFollowings(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => 'nullable|integer|max:1000'
        ]);

        $perPage = $request->query->getInt('per_page', 15);

        /** @var User $foundUser */
        $followings = $this->service->getUserFollowings($this->authUser, $perPage);

        return  response()->json(FollowingCollectionResource::make($followings));
    }

    /**
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function getFollowingsSearch(Request $request, int $userId): JsonResponse
    {
        $request->merge(['user_id' => $userId])->validate([
            'user_id' => 'required|integer|exists:Wo_Users,user_id',
            'search' => 'required|string|min:3,max:50|regex:/^[a-zA-Z0-9_ ]+$/',
            'per_page' => 'nullable|integer|max:1000'
        ]);

        $perPage = $request->query->getInt('per_page', 15);

        $search = $request->query->get('search');

        $followers = $this->service->getFollowingsSearch($userId, $perPage, $search);

        return response()->json(UserCollectionResource::make($followers));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getFollowingRequests(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => 'nullable|integer|max:1000'
        ]);

        $perPage = $request->query->getInt('per_page', 15);

        $followingRequests = $this->service->getFollowingRequests($this->authUser->user_id, $perPage);

        return  response()->json(FollowingRequestCollectionResource::make($followingRequests));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getFollowRecommendations(Request $request): JsonResponse
    {
        $request->validate(['per_page' => 'nullable|integer|max:1000']);

        $perPage = $request->query->getInt('per_page', 15);

        $followRecommendations = $this->service->getFollowRecommendations($this->authUser, $perPage);

        return response()->json(UserCollectionResource::make($followRecommendations));
    }

    /**
     * @param Request $request
     * @param int|null $userId
     * @return JsonResponse
     */
    public function search(Request $request, int $userId = null): JsonResponse
    {
        $validated = $request->validate([
            'search_by' => 'string|in:followers,followings',
            'search' => 'required|string'
        ]);

        $userId ??= $this->authUser->user_id;
        $searchBy = $request->query('search_by');
        $search = $request->query('search');
        $founds = $this->service->search($userId, $search, $searchBy);

        return response()->json($founds);
    }

    /**
     * @param int $userId
     * @return JsonResponse
     */
    public function acceptFollowingRequest(int $userId): JsonResponse
    {
        $result = $this->service->acceptFollowingRequest($this->authUser, $userId);

        if ($result) {
            $message = 'Accepted';
            $status = JsonResponse::HTTP_OK;
        } else {
            $message = 'Request not found';
            $status = JsonResponse::HTTP_BAD_REQUEST;
        }

        return response()->json(['message' => $message], $status);
    }

    /**
     * @param int $userId
     * @return JsonResponse
     */
    public function declineFollowingRequest(int $userId): JsonResponse
    {
        $result = $this->service->declineFollowingRequest($this->authUser, $userId);

        if ($result) {
            $message = 'Declined';
            $status = JsonResponse::HTTP_OK;
        } else {
            $message = 'Request not found';
            $status = JsonResponse::HTTP_BAD_REQUEST;
        }

        return response()->json(['message' => $message], $status);
    }
}
