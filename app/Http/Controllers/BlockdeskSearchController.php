<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Collections\BlogCollectionResource;
use App\Models\User;
use App\Services\BlogSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * class BlockdeskSearchController
 * @package App\Http\Controllers
 */
class BlockdeskSearchController extends Controller
{

    /**
     * @var User|null $authUser
     */
    private ?User $authUser;

    private BlogSearchService $service;

    public function __construct(BlogSearchService $service)
    {
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate(
            [
                'search' => 'string|regex:/^[a-zA-Z0-9 ]+$/|min:3|max:100',
            ],
            [
                'search.regex' => ":attribute only latter and number symbols",
            ]
        );

        $userId = is_null($this->authUser) ? $this->authUser : $this->authUser->user_id;
        $perPage = $request->query->getInt('per_page');
        $blogs = $this->service->search($userId, $validated['search'], $perPage);

        return response()->json(
            $blogs ? BlogCollectionResource::make($blogs) : []
        );
    }
}
