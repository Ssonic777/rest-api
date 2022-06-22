<?php

declare(strict_types=1);

namespace App\Services;

use App\Collections\MessageCollection;
use App\Models\Message;
use App\Models\User;
use App\Repositories\MessageRepository;
use App\Services\Contracts\Messages\MessageServiceInterface;
use App\Traits\FileTrait;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * Class MessageService
 * @package App\Services
 */
class MessageService implements MessageServiceInterface
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
     * @return \Illuminate\Support\Collection
     */
    public function getChatList(User $user): \Illuminate\Support\Collection
    {
        $fromMessages = $user->fromMessages()->get();
        $toMessages = $user->toMessages()->get();

        $messages = $fromMessages->merge($toMessages)->sortByDesc('id');
        return $this->makeChatListForUser($user, $messages);
    }

    /**
     * @param User $fromUser
     * @param array $data
     * @return Message
     */
    public function storeFromMessage(User $fromUser, array $data): Message
    {
        if (array_key_exists('media', $data)) {
            ['full_path' => $data['media'], 'user_file_name' => $data['mediaFileName']] = $this->uploadFile(Message::MESSAGE_MEDIA_PATH, $data['media']);
        }

        /** @var Message $storedMessage */
        $storedMessage = $fromUser->fromMessages()->create($data);

        return $storedMessage;
    }

    /**
     * @param User $auth
     * @param int $userId
     * @return Collection
     */
    public function showMessages(User $auth, int $userId): Collection
    {
        $messagesForUser = $this->repository->getMessages($auth->user_id, $userId, ['replies', 'replied']);
        $messagesForUser->each(fn($msg) => $this->makeSeen($auth, $msg));

        return $messagesForUser;
    }

    /**
     * @param User $user
     * @param int $messageId
     * @param array $data
     * @return Message
     */
    public function updateMessage(User $user, int $messageId, array $data): Message
    {
        /** @var Message $foundMessage */
        $foundMessage = $this->repository->find($messageId);
        $this->checkRight($user, 'update', $foundMessage);

        if (array_key_exists('media', $data)) {
            [
                'full_path' => $data['media'],
                'user_file_name' => $data['mediaFileName']
            ] = $this->updateFile(Message::MESSAGE_MEDIA_PATH, $foundMessage->media, $data['media']);
        }

        $foundMessage->update($data);

        return $foundMessage->refresh();
    }

    /**
     * @param User $user
     * @param int $messageId
     */
    public function deleteMessage(User $user, int $messageId): void
    {
        $foundUserMessage = $this->repository->findUserMessage($user->user_id, $messageId);
        $this->checkRight($user, 'delete', $foundUserMessage);
        $this->deleteFile(Message::MESSAGE_MEDIA_PATH, $foundUserMessage->media);
        $foundUserMessage->delete();
    }

    /**
     * @param User $forUser
     * @param MessageCollection $messages
     * @return \Illuminate\Support\Collection
     */
    private function makeChatListForUser(User $forUser, MessageCollection $messages): \Illuminate\Support\Collection
    {
        $chatList = collect();

        /** @var Message $msg */
        foreach ($messages as $msg) {
            $from = $chatList->contains('from_id', '=', $forUser->getUserId());
            $to = $chatList->contains('to_id', '=', $msg->getToId());

            if ($from && $to) {
                continue;
            }

            $to = $chatList->contains('to_id', '=', $forUser->getUserId());
            $from = $chatList->contains('from_id', '=', $msg->getFromId());

            if ($to && $from) {
                continue;
            }

            // Set count UnRead Messages
            $msg->setAttribute('unread_msgs', $messages->getCountUnReadMsgs($msg->getFromId()));

            $chatList->add($msg);
        }

        return $chatList;
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
