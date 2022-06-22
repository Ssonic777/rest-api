<?php

namespace App\Services\Contracts\Messages;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface MessageServiceInterface
 * MessageServiceInterface for sending Messages
 * @package App\Services\Contracts\Messages
 */
interface MessageServiceInterface
{
    /**
     * Get All Chat List
     * @param User $user
     * @return \Illuminate\Support\Collection
     */
    public function getChatList(User $user): \Illuminate\Support\Collection;

    /**
     * Store message form User
     * @param User $fromUser
     * @param array $data
     * @return Message
     */
    public function storeFromMessage(User $fromUser, array $data): Message;

    /**
     * Show concrete Messages with User
     * @param User $auth
     * @param int $userId
     * @return Collection
     */
    public function showMessages(User $auth, int $userId): Collection;

    /**
     * Update message
     * @param User $user
     * @param int $messageId
     * @param array $data
     * @return Message
     */
    public function updateMessage(User $user, int $messageId, array $data): Message;

    /**
     * Delete message
     * @param User $user
     * @param int $messageId
     */
    public function deleteMessage(User $user, int $messageId): void;
}
