<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\PostHideService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * class PostHideController
 * @package App\Http\Controllers
 */
class PostHideController extends Controller
{
    /**
     * @var User|null $authUser
     */
    private ?User $authUser;

    private PostHideService $service;

    public function __construct(PostHideService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
    }

    /**
     * @param int $postId
     * @return JsonResponse
     */
    public function hideToggle(int $postId): JsonResponse
    {
        $result = $this->service->hide($this->authUser, $postId);

        return response()->json($result, Response::HTTP_ACCEPTED);
    }
}
