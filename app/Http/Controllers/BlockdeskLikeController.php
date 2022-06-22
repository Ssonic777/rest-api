<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BlockdeskLikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * class BlockdeskLikeController
 * @package App\Http\Controllers
 */
class BlockdeskLikeController extends Controller
{
    /**
     * @var User|null $authUser
     */
    private ?User $authUser;

    /**
     * @var BlockdeskLikeService $service
     */
    private BlockdeskLikeService $service;

    public function __construct(BlockdeskLikeService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
    }

    /**
     * @param int $blogId
     * @return JsonResponse
     */
    public function toggleLike(int $blogId): JsonResponse
    {
        $result = $this->service->toggleLike($this->authUser, $blogId);

        return response()->json($result, Response::HTTP_ACCEPTED);
    }
}
