<?php

namespace App\Http\Controllers;

use App\Http\Resources\Group\GroupChatResource;
use App\Models\User;
use App\Services\GroupChatService;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class GroupChatController extends Controller
{
    use FileTrait;

    /**
    * @var User $authUser
    */
    private ?User $authUser;

    /**
    * @var GroupChatService $service
    */
    public GroupChatService $service;

    public function __construct(GroupChatService $service)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->user();
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $groupChats = $this->service->getGroupChats($this->authUser, $request);

        return response()->json($groupChats);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(Request $request): JsonResponse
    {
        $fields = $request->validate([
            'group_name' => 'required|string|min:3|max:100',
            'avatar' => 'file|mimes:jpg,bmp,png,gif,jpeg',
            'users' => 'required|array',
            'users.*' => 'required|integer'
        ]);

        $storedGroup = $this->service->storeGroupChat($this->authUser, $fields);

        return response()->json(['chat_id' => $storedGroup->group_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $foundGroupChat = $this->service->repository->find($id);

        return response()->json(GroupChatResource::make($foundGroupChat));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $updatedGroupChat = $this->service->updateGroupChat($id, $request->all());

        return response()->json(['status' => true], Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteGroupChat($this->authUser, $id);

        return response()->json(['status' => true]);
    }
}
