<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Group;
use App\Services\GroupMediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * class GroupMediaController
 * @package App\Http\Controllers
 */
class GroupMediaController extends Controller
{

    /**
     * @var GroupMediaService $service
     */
    private GroupMediaService $service;

    public function __construct(GroupMediaService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @param int $groupId
     * @return JsonResponse
     */
    public function __invoke(Request $request, int $groupId): JsonResponse
    {
        $request->merge(['group_id' => $groupId])->validate([
            '_method' => 'required|string|in:PUT',
            'group_id' => 'required|integer|exists:' . Group::class . ',id',
            'avatar' => 'required_without:cover|file|mimetypes:' . implode(',', Group::MEDIA_MIMETYPES),
            'cover' => 'required_without:avatar|file|mimetypes:' . implode(',', Group::MEDIA_MIMETYPES)
        ]);

        $validated = $request->only('avatar', 'cover');
        $result = $this->service->updateGroupMedia($groupId, $validated);

        return response()->json($result);
    }
}
