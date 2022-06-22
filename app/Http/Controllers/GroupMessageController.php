<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Group\GroupChatResource;
use App\Http\Resources\MessageResource;
use App\Models\User;
use App\Services\Contracts\Messages\GroupMessageServiceInterface;
use App\Services\GroupMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * class GroupMessageController
 * @package App\Http\Contracts
 */
class GroupMessageController extends Controller
{

    /**
    * @var User|null $authUser
    */
    private ?User $authUser;

    /**
    * @var GroupMessageServiceInterface $service
    */
    private GroupMessageServiceInterface $service;

    public function __construct(GroupMessageService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard('api')->user();
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $groupMessageChatList = $this->service->getGroupChatList($this->authUser);

        return response()->json(GroupChatResource::collection($groupMessageChatList));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'group_id' => 'required|integer|exists:Wo_GroupChat,group_id',
            'text' => 'string',
            'media' => 'file'
        ]);

        $storedMessage = $this->service->storeFromMessage($this->authUser, $validated);

        return response()->json(MessageResource::make($storedMessage), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $groupId
     * @return JsonResponse
     */
    public function show(int $groupId): JsonResponse
    {
        $messages = $this->service->showGroupMessages($this->authUser, $groupId);

        return response()->json(MessageResource::collection($messages));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $groupMessageId
     * @return JsonResponse
     */
    public function update(Request $request, int $messageId): JsonResponse
    {
        $validated = $request->merge(['message_id' => $messageId])->validate([
            'group_id' => 'required|integer|exists:Wo_GroupChat,group_id',
            'message_id' => 'required|integer|exists:Wo_Messages,id',
            'text' => 'string',
            'media' => 'file'
        ]);

        $updatedMessage = $this->service->updateGroupMessage($this->authUser, $messageId, $validated);

        return response()->json(MessageResource::make($updatedMessage));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $groupMessageId
     * @return Response
     */
    public function destroy(int $groupMessageId): Response
    {
        $this->service->deleteGroupMessage($this->authUser, $groupMessageId);

        return response()->noContent();
    }
}
