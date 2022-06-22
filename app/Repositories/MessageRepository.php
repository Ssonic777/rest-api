<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Message;
use App\Models\User;
use App\Repositories\Base\BaseModelRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class MessageRepository
 * @package App\Repositories
 */
class MessageRepository extends BaseModelRepository
{

    protected function getModel(): string
    {
        return Message::class;
    }

    /**
     * @param int $authUserId
     * @param int $userId
     * @param array $with
     * @return Collection
     */
    public function getMessages(int $authUserId, int $userId, array $with = []): Collection
    {
        return $this->getModelClone()->newQuery()
                                    ->where([
                                        ['from_id', '=', $authUserId, 'and'],
                                        ['to_id', '=', $userId, 'or'],

                                        ['from_id', '=', $userId, 'and'],
                                        ['to_id', '=', $authUserId, 'or'],
                                    ])
                                    ->with($with)
                                    ->get();
    }

    /**
     * @param int $fromUserId
     * @return Collection
     */
    public function getFromMessages(int $fromUserId): Collection
    {
        return $this->getModelClone()->newQuery()
                                    ->orderByDesc('id')
                                    ->where('from_id', $fromUserId)
                                    // ->groupBy('to_id')
                                    ->get();
    }

    /**
     * @param int $toUserId
     * @return Collection
     */
    public function getToMessages(int $toUserId): Collection
    {
        return $this->getModelClone()->newQuery()
                                    ->orderByDesc('id')
                                    ->where('to_id', $toUserId)
                                    // ->distinct('from_id')
                                    ->get();
    }

    /**
     * @param int $fromId
     * @param int $messageId
     * @return Message
     */
    public function findUserMessage(int $fromId, int $messageId): Message
    {
        return $this->getModelClone()->newQuery()
                                    ->where('from_id', '=', $fromId)
                                    ->findOrFail($messageId);
    }
}
