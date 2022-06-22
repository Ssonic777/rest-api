<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Group\GroupAdditionalDataResource;
use App\Http\Resources\Group\GroupSettingResource;
use App\Services\GroupAdditionalDataService;
use App\Services\GroupSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * class GroupSettingController
 * @package App\Http\Controllers
 */
class GroupAdditionalDataController extends Controller
{
    /**
     * @var GroupAdditionalDataService
     */
    private GroupAdditionalDataService $service;

    public function __construct(GroupAdditionalDataService $service)
    {
        $this->middleware('auth:api');
        $this->service = $service;
    }

    /**
     * @param int $groupId
     * @return JsonResponse
     */
    public function show(int $groupId): JsonResponse
    {
        $showAdditional = $this->service->showAdditionalData($groupId);

        return response()->json(GroupAdditionalDataResource::make($showAdditional));
    }

    /**
     * @param Request $request
     * @param int $groupId
     * @return JsonResponse
     */
    public function update(Request $request, int $groupId): JsonResponse
    {
        $validated = $request->merge(['group_id' => $groupId])->validate([
            'group_id' => 'required|integer|exists:Wo_Groups,id',
            'phone' => "nullable|string",
            'email' => "nullable|string|email",
            'website' => "nullable|string|url",
            'location' => "nullable|string",
            'facebook' => "nullable|string|url",
            'instagram' => "nullable|string|url",
            'twitter' => "nullable|string|url",
            'vkontakte' => "nullable|string|url",
            'youtube' => "nullable|string|url",
            'linkedin' => "nullable|string|url"
        ]);

        $updatedSetting = $this->service->updateAdditionalData($groupId, $validated);

        return response()->json(GroupAdditionalDataResource::make($updatedSetting));
    }
}
