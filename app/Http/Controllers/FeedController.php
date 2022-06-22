<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Collections\PostCollectionResource;
use App\Models\User;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\CursorPaginator;

/**
 * Class FeedController
 * @package App\Http\Controllers
 */
class FeedController extends Controller
{
    /**
     * @var User|null
     */
    private ?User $authUser;

    /**
    * @var PostService $service
    */
    private PostService $service;

    public function __construct(PostService $service)
    {
        $this->middleware('auth:api');
        $this->service = $service;
        $this->authUser = auth()->guard('api')->user();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => 'nullable|integer|max:1000'
        ]);

        $perPage = $request->query->getInt('per_page');
        $posts = $this->service->getFeed($this->authUser, $perPage);

        return response()->json(PostCollectionResource::make($posts));
    }

    /**
     * @param Request $request
     * @return  JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => 'nullable|integer|max:1000',
            'search' => 'string|required|regex:' . $this->service::CLEAR_STRING . '|min:3|max:100',
        ]);

        $perPage = $request->query->getInt('per_page', 15);
        $search = $request->query->get('search');

        $posts = $this->service->searchFeed($this->authUser, $search, $perPage);

        return response()->json(PostCollectionResource::make($posts));
    }
}
