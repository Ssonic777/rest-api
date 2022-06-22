<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\PostShareService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Post;
use App\Http\Resources\PostResource;

/**
 * Class PostShareController
 * @package App\Http\Controllers
 */
class PostShareController extends Controller
{
    private ?User $authUser;

    private PostShareService $service;

    /**
     * @param PostShareService $service
     */
    public function __construct(PostShareService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard()->user();
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @param int $postId
     * @return JsonResponse
     */
    public function shareOnTimeline(Request $request, int $postId): JsonResponse
    {
        $validated = $request->merge(['parent_id' => $postId])->validate([
            'parent_id' => 'required|integer|exists:Wo_Posts,post_id',
            'post_text' => 'string|max:5000',
            'post_privacy' => 'string|in:' . implode(',', Post::$privacyOptions),
            'comments_status' => 'boolean',
            'enable_notifications' => 'boolean',
        ]);

        $sharedPost = $this->service->sharePost($this->authUser, $validated);

        return response()->json(PostResource::make($sharedPost));
    }
}
