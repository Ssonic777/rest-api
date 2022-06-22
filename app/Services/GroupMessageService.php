<?php

namespace App\Services;

use App\Models\GroupChat;
use App\Models\Message;
use App\Models\User;
use App\Repositories\GroupChatRepository;
use App\Repositories\MessageRepository;
use App\Services\Contracts\Messages\GroupMessageServiceInterface;
use App\Traits\FileTrait;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class GroupMessageService implements GroupMessageServiceInterface
{
    use FileTrait;

    private const DONT_RIGHT_MSG = 'You don\'t have enough rights';

    /**
    * @var MessageRepository $repository
    */
    private MessageRepository $repository;

    public function __construct(MessageRepository $messageRepository)
    {
        $this->repository = $messageRepository;
    }

    /**
     * @param User $user
     * @return Collection
     */
    public function getGroupChatList(User $user): Collection
    {
        $groupsChatListMessages = $user->groupChats()->get();

        $groupsChatListMessages = $groupsChatListMessages->map(function (GroupChat $groupChat): GroupChat {
            if ($groupChat->messages->count()) {
                $lastMsg = $groupChat->messages()->latest('id')->first();
                $groupChat->setRelation('messages', collect()->add($lastMsg));
            }

            return $groupChat;
        });

        return $groupsChatListMessages->sortByDesc('messages.*.id');
    }

    /**
     * @param User $fromUser
     * @param array $data
     * @return Message
     */
    public function storeFromMessage(User $fromUser, array $data): Message
    {
        if (array_key_exists('media', $data)) {
            [
                'full_path' => $data['media'],
                'user_file_name' => $data['mediaFileName']
            ] = $this->uploadFile(Message::MESSAGE_MEDIA_PATH, $data['media']);
        }

        /** @var Message $storedMessage */
        $storedMessage = $fromUser->fromMessages()->create($data);

        return $storedMessage->fresh();
    }

    /**
     * @param User $user
     * @param int $groupId
     * @return Collection
     */
    public function showGroupMessages(User $user, int $groupId): Collection
    {
        /** @var GroupChat $foundGroup */
        $foundGroupChat = app()->call(GroupChatRepository::class . '@find', ['id' => $groupId]);
        $this->checkRight($user, 'view', $foundGroupChat);
        $foundGroupChat = $foundGroupChat->messages()->get();
        // If you have to mark a message as read for a Group Chat
        // $groupChatMessages->each(fn($msg) => $this->makeSeen($user, $msg));

        return $foundGroupChat;
    }

    /**
     * @param User $user
     * @param int $messageId
     * @param array $data
     * @return Message
     */
    public function updateGroupMessage(User $user, int $messageId, array $data): Message
    {
        /** @var Message $foundMessage */
        $foundGroupMessage = $this->repository->find($messageId);
        $this->checkRight($user, 'update', $foundGroupMessage);

        if (array_key_exists('media', $data)) {
            [
                'full_path' => $data['media'],
                'user_file_name' => $data['mediaFileName']
            ] = $this->updateFile(Message::MESSAGE_MEDIA_PATH, $foundGroupMessage->media, $data['media']);
        }

        $foundGroupMessage->update($data);

        return $foundGroupMessage->refresh();
    }

    /**
     * @param User $user
     * @param int $groupMessageId
     */
    public function deleteGroupMessage(User $user, int $groupMessageId): void
    {
        $foundUserGroupMessage = $this->repository->find($groupMessageId);
        $this->checkRight($user, 'delete', $foundUserGroupMessage);
        $this->deleteFile(Message::MESSAGE_MEDIA_PATH, $foundUserGroupMessage->media);
        $foundUserGroupMessage->delete();
    }

    /**
     * @param User $forUser
     * @param Message $message
     */
    private function makeSeen(User $forUser, Message $message): void
    {
        if ($forUser->user_id == $message->to_id && $message->getAttributes()['seen'] === Message::MESSAGE_NOT_SEEN) {
            $message->seen = time();
            $message->save();
        }
    }

    /**
     * @param User $user
     * @param string $abilities
     * @param array $arguments
     */
    private function checkRight(User $user, string $abilities, $arguments = []): void
    {
        if ($user->cant($abilities, $arguments)) {
            throw new BadRequestException(self::DONT_RIGHT_MSG);
        }
    }
}
