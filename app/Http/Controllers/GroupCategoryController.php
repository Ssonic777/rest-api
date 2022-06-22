<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Group\GroupCategoryResource;
use App\Services\GroupCategoryService;
use Illuminate\Http\JsonResponse;

/**
 * Class GroupCategoryController
 * @package App\Http\Controllers
 */
class GroupCategoryController extends Controller
{
    /**
     * @var GroupCategoryService $service
     */
    private GroupCategoryService $service;

    public function __construct(GroupCategoryService $service)
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
        return response()->json(GroupCategoryResource::collection($this->service->getGroupCategories()));
    }
}
