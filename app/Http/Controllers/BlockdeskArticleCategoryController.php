<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\BlockdeskArticleCategoryResource;
use App\Services\BlockdeskArticleCategoryService;
use Illuminate\Http\JsonResponse;

/**
 * Class ArticleCategoryController
 * @package App\Http\Controllers
 */
class BlockdeskArticleCategoryController extends Controller
{
    /**
     * @var BlockdeskArticleCategoryService $service
     */
    private BlockdeskArticleCategoryService $service;

    public function __construct(BlockdeskArticleCategoryService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(BlockdeskArticleCategoryResource::collection($this->service->getBlockdeskArticleCategories()));
    }
}
