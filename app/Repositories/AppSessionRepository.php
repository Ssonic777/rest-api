<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\AppSession;
use App\Repositories\Base\BaseModelRepository;
use Illuminate\Support\Collection;

/**
 * class AppSessionRepository
 * @package App\Repositories
 */
class AppSessionRepository extends BaseModelRepository
{

    public const DEFAULT_SELECT = [
        'id',
        'session_id',
        'platform',
        'platform_details',
        'time'
    ];

    public function initializeDefaultData(): void
    {
        $this->setSelect(self::DEFAULT_SELECT);
    }

    /**
     * @return string
     */
    protected function getModel(): string
    {
        return AppSession::class;
    }

    /**
     * @param int $userId
     * @return Collection
     */
    public function getUserSessions(int $userId): Collection
    {
        return $this->getModelClone()->newQuery()
                                    ->where('user_id', $userId)
                                    ->where('time', '>', now()->subMinutes(config('auth-refresh-token.ttl'))->timestamp)
                                    ->get($this->getSelect());
    }

    /**
     * @param int $userId
     * @param string $sessionId
     * @return AppSession
     */
    public function findUserSessionBySessionId(int $userId, string $sessionId): AppSession
    {
        return $this->getModelClone()->newQuery()
                                    ->where('user_id', $userId)
                                    ->where('session_id', $sessionId)
                                    ->firstOrFail();
    }

    /**
     * @param int $userId
     * @param int $id
     * @return AppSession
     */
    public function findUserSessionById(int $userId, int $id): AppSession
    {
        return $this->getModelClone()->newQuery()
                                    ->where('user_id', $userId)
                                    ->findOrFail($id);
    }
}
