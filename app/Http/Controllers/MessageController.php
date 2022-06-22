<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Models\User;
use App\Services\Contracts\Messages\MessageServiceInterface;
use App\Services\MessageService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\Constraint\JsonMatchesErrorMessageProvider;

class MessageController extends Controller
{

    /**
    * @var User $authUser
    */
    private ?User $authUser;

    /**
    * @var MessageServiceInterface $service
    */
    private MessageServiceInterface $service;

    /**
    * @var UserService $userService
    */
    private UserService $userService;

    public function __construct(MessageService $service, UserService $userService)
    {
        $this->middleware('auth:api');
        $this->authUser = auth()->guard()->user();
        $this->service = $service;
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $messageChatList = $this->service->getChatList($this->authUser);

        return response()->json(MessageResource::collection($messageChatList));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'to_id' => 'required|integer|exists:Wo_Users,user_id',
            'text' => 'string',
            'media' => 'file',
            'reply_msg' => 'integer|exists:Wo_Messages,id'
        ]);

        $storedMessage = $this->service->storeFromMessage($this->authUser, $validated);

        return response()->json(MessageResource::make($storedMessage), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $userId): JsonResponse
    {
        $messages = $this->service->showMessages($this->authUser, $userId);

        return response()->json(MessageResource::collection($messages));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $messageId): JsonResponse
    {
        $validated = $request->merge(['message_id' => $messageId])->validate([
            'message_id' => 'required|integer|exists:Wo_Messages,id',
            'text' => 'string',
            'media' => 'file'
        ]);

        $updatedMessage = $this->service->updateMessage($this->authUser, $messageId, $validated);

        return response()->json(MessageResource::make($updatedMessage));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $messageId): Response
    {
        $this->service->deleteMessage($this->authUser, $messageId);

        return response()->noContent();
    }
}
