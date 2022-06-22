<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\BlockdeskCommentReportService;
use Illuminate\Http\JsonResponse;

/**
 * class BlockdeskCommentReportController
 * @package App\Http\Controllers
 */
class BlockdeskCommentReportController extends Controller
{
    private ?User $authUser;
    private BlockdeskCommentReportService $service;

    /**
     * @param BlockdeskCommentReportService $service
     */
    public function __construct(BlockdeskCommentReportService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @param int $articleId
     * @param int $commentId
     * @return JsonResponse
     */
    public function reportComment(Request $request, int $articleId, int $commentId): JsonResponse
    {
        $validated = $request->merge([
            'article_id' => $articleId,
            'comment_id' => $commentId,
        ])->validate([
            'article_id' => 'required|integer|exists:Wo_Blog,id',
            'comment_id' => 'required|integer|exists:Wo_Comments,id',
            'text' => 'required|string|min:4|max:40',
        ]);

        $data = $this->service->reportBlogComment($this->authUser, $validated['article_id'], $validated['comment_id'], $validated['text']);

        return response()->json($data, JsonResponse::HTTP_ACCEPTED);
    }

    /**
     * @param Request $request
     * @param int $articleId
     * @param int $commentId
     * @return JsonResponse
     */
    public function withdrawCommentReport(Request $request, int $articleId, int $commentId): JsonResponse
    {
        $validated = $request->merge([
            'article_id' => $articleId,
            'comment_id' => $commentId,
        ])->validate([
            'article_id' => 'integer|required|exists:Wo_Blog,id',
            'comment_id' => 'integer|required|exists:Wo_Comments,id',
        ]);

        $data = $this->service->withdrawBlogCommentReport($this->authUser, $validated['article_id'], $validated['comment_id']);

        return response()->json($data, JsonResponse::HTTP_ACCEPTED);
    }
}
