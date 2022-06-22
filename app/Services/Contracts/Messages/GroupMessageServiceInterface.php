<?php

namespace App\Services\Contracts\Messages;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface GroupMessageServiceInterface
 * GroupMessageServiceInterface for sending Group Messages
 * @package App\Services\Contracts\Messages
 */
interface GroupMessageServiceInterface
{
    /**
     * Get All Group Chat List with latest Messages
     * @param User $user
     * @return Collection
     */
    public function getGroupChatList(User $user): Collection;

    /**
     * Store Group message from User
     * @param User $fromUser
     * @param array $data
     * @return Message
     */
    public function storeFromMessage(User $fromUser, array $data): Message;

    /**
     * Show concrete Group Messages
     * @param User $user
     * @param int $groupId
     * @return Collection
     */
    public function showGroupMessages(User $user, int $groupId): Collection;

    /**
     * Update Group Message
     * @param User $user
     * @param int $messageId
     * @param array $data
     * @return Message
     */
    public function updateGroupMessage(User $user, int $messageId, array $data): Message;

    /**
     * Delete Group Message
     * @param User $user
     * @param int $groupMessageId
     */
    public function deleteGroupMessage(User $user, int $groupMessageId): void;
}
