<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\PostReportService;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * class PostReportController
 * @package App\Http\Controller
 */
class PostReportController extends Controller
{
    private ?User $authUser;

    private PostService $service;

    private PostReportService $reportService;

    /**
     * @param PostService $service
     * @param PostReportService $reportService
     */
    public function __construct(PostService $service, PostReportService $reportService)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
        $this->reportService = $reportService;
    }

    /**
     * @param Request $request
     * @param int $postId
     * @return JsonResponse
     */
    public function report(Request $request, int $postId): JsonResponse
    {
        $validated = $request->merge(['post_id' => $postId])->validate([
            'post_id' => 'required|integer|exists:Wo_Posts,post_id',
            'text' => 'required|string|min:4|max:40',
        ]);

        $response = $this->reportService->report($this->authUser, $validated);

        return response()->json($response);
    }

    /**
     * @param Request $request
     * @param int $postId
     * @return JsonResponse
     */
    public function withdraw(Request $request, int $postId): JsonResponse
    {
        $validated = $request->merge(['post_id' => $postId])->validate([
            'post_id' => 'required|integer|exists:Wo_Posts,post_id',
        ]);

        $postId = $validated['post_id'];
        $response = $this->reportService->withdraw($this->authUser, $postId);

        return response()->json($response);
    }
}
