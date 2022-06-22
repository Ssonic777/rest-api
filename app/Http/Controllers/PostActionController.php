<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\PostPinService;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;

/**
 * Class PostActionController
 * @package App\Http\Controllers
 */
class PostActionController extends Controller
{
    /**
    * @var PostService $service
    */
    private PostService $service;

    /**
     * @var PostPinService $pinService
     */
    private PostPinService $pinService;

    /**
    * @var User|null $authUser
    */
    private ?User $authUser;

    /**
     * @param PostService $service
     * @param PostPinService $pinService
     */
    public function __construct(PostService $service, PostPinService $pinService)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard()->user();
        $this->service = $service;
        $this->pinService = $pinService;
    }

    /**
     * @param int $postId
     * @return JsonResponse
     */
    public function pinToggle(int $postId): JsonResponse
    {
        $response = $this->pinService->pinToggle($postId, $this->authUser->user_id);

        return response()->json($response);
    }
}
