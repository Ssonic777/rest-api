<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\FCMToken;
use App\Repositories\Base\BaseModelRepository;

/**
 * class FCMTokenRepository
 * @package App\Repositories
 */
class FCMTokenRepository extends BaseModelRepository
{
    /**
     * @return string
     */
    protected function getModel(): string
    {
        return FCMToken::class;
    }

    /**
     * @param int $userId
     * @param string $userAgent
     * @return FCMToken|null
     */
    public function findByUserAgent(int $userId, string $userAgent): ?FCMToken
    {
        return $this->getModelClone()->newQuery()
                                    ->where('user_id', $userId)
                                    ->where('user_agent', $userAgent)
                                    ->first();
    }

    /**
     * @param int $userId
     * @param array $deviceTokens
     * @return bool
     */
    public function deleteDeviceTokens(int $userId, array $deviceTokens): bool
    {
        return (bool) $this->getModelClone()->newQuery()
                                    ->where('user_id', '=', $userId)
                                    ->whereIn('device_token', $deviceTokens)
                                    ->delete();
    }

    /**
     * @param int $userId
     * @param string $deviceId
     * @return bool
     */
    public function deleteByDeviceId(int $userId, string $deviceId): bool
    {
        return (bool) $this->getModelClone()->newQuery()
                                ->where('user_id', '=', $userId)
                                ->where('device_id', '=', $deviceId)
                                ->delete();
    }
}
