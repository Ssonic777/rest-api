<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Collections\PostCollectionResource;
use App\Models\User;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class SearchController
 * @package App\Http\Controllers
 */
class SearchController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $result = [];

        $type = $request->get('type');

        if ($type == 'users') {
            $search = User::search();
            $search->apply($request);
            $result = $search->find(10);
        } elseif ($type == 'feeds') {
            /** @var PostService $postService */
            $postService = resolve(PostService::class);
            $searchData = $request->all(['first_name', 'email', 'username']);
            $foundPosts = $postService->repository->searchPosts(auth()->guard('api')->user(), $searchData, (int)$request->get('perPage'));

            $result = PostCollectionResource::make($foundPosts);
        }

        return response()->json($result);
    }
}
