<?php

namespace App\Http\Controllers;

use App\Http\Resources\Group\GroupChatResource;
use App\Services\GroupChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * Class GroupChatActionController
 * @package App\Http\Controllers
 */
class GroupChatActionController extends Controller
{

    /**
    * @var GroupChatService $service
    */
    private GroupChatService $service;

    public function __construct(GroupChatService $service)
    {
        $this->middleware('auth:api');
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @param int $groupId
     * @return Response
     * @throws BadRequestException
     */
    public function addUserInGroupChat(Request $request, int $groupId): Response
    {
        $fields = $request->merge(['group_id' => $groupId])->validate([
            'user_id' => 'required|integer|exists:Wo_Users,user_id',
            'group_id' => 'required|integer|exists:Wo_GroupChat,group_id'
        ]);

        $this->service->addUserInGroupChat($fields);

        return response()->noContent(Response::HTTP_ACCEPTED);
    }

    /**
     * @param int $groupId
     * @return JsonResponse
     */
    public function getUsersFromUserChat(int $groupId): JsonResponse
    {
        $foundGroupChat = $this->service->repository->find($groupId);

        return response()->json(GroupChatResource::make($foundGroupChat));
    }

    /**
     * @throws \Exception
     */
    public function removeUserFromChatUser(Request $request, int $groupId): Response
    {
        $fields = $request->merge(['group_id' => $groupId])->validate([
            'user_id' => 'required|integer|exists:Wo_Users,user_id',
            'group_id' => 'required|integer|exists:Wo_GroupChat,group_id'
        ]);

        $this->service->removeUserFromChatGroup($fields);

        return response()->noContent(Response::HTTP_ACCEPTED);
    }
}
