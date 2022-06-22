<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\AuthRefreshToken;
use App\Repositories\Base\BaseModelRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * class AuthRefreshTokenRepository
 * @package App\Repository
 */
class AuthRefreshTokenRepository extends BaseModelRepository
{

    public const DEFAULT_SELECT = [
        'id',
        'device_id',
        'user_id',
        'platform',
        'platform_version',
        'browser',
        'browser_version',
        'expire',
        'updated_at',
    ];

    /**
     * @return string
     */
    protected function getModel(): string
    {
        return AuthRefreshToken::class;
    }

    public function initializeDefaultData(): void
    {
        $this->setSelect(self::DEFAULT_SELECT);
    }

    /**
     * @param int $userId
     * @return Collection
     */
    public function getUserSessions(int $userId): Collection
    {
        return $this->getModelClone()->newQuery()
                                    ->where('user_id', $userId)
                                    ->whereDate('updated_at', '>', now()->subMinutes(config('auth-refresh-token.ttl')))
                                    ->get($this->getSelect());
    }

    /**
     * @param int $userId
     * @param string $deviceId
     * @return AuthRefreshToken|null
     */
    public function findDeviceIdByUserId(int $userId, string $deviceId): ?AuthRefreshToken
    {
        return $this->getModelClone()->newQuery()
                                    ->where('user_id', $userId)
                                    ->where('device_id', $deviceId)
                                    ->first($this->getSelect());
    }

    /**
     * @param string $filed
     * @param string $value
     * @param string $refreshToken
     * @return AuthRefreshToken|null
     */
    public function findRefreshTokenBy(string $filed, string $value, string $refreshToken): ?AuthRefreshToken
    {
        return $this->getModelClone()->newQuery()
                                    ->where($filed, $value)
                                    ->where('refresh_token', $refreshToken)
                                    ->first($this->getSelect());
    }

    /**
     * @param string $field
     * @param string $value
     * @param array $data
     * @return AuthRefreshToken
     */
    public function saveRefreshTokenBy(string $field, string $value, array $data): AuthRefreshToken
    {
        return $this->getModelClone()->newQuery()
                                    ->where('user_id', $data['user_id'])
                                    ->updateOrCreate([$field => $value], $data);
    }

    /**
     * @param int $userId
     * @param string $deviceId
     * @return AuthRefreshToken
     */
    public function findUserSessionByDeviceId(int $userId, string $deviceId): AuthRefreshToken
    {
        return $this->getModelClone()->newQuery()
            ->where('user_id', $userId)
            ->where('device_id', $deviceId)
            ->firstOrFail();
    }

    /**
     * @param int $userId
     * @param int $sessionId
     * @return AuthRefreshToken
     */
    public function findUserSessionById(int $userId, int $sessionId): AuthRefreshToken
    {
        return $this->getModelClone()->newQuery()
                                    ->where('user_id', $userId)
                                    ->findOrFail($sessionId);
    }
}
