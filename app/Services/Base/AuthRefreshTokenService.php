<?php

declare(strict_types=1);

namespace App\Services\Base;

use App\Exceptions\Auth\RefreshTokenExpiredException;
use App\Exceptions\Auth\RefreshTokenNotFoundException;
use App\Models\AuthRefreshToken;
use App\Models\User;
use App\ProjectClass\GenerateAuthRefreshToken;
use App\Repositories\AuthRefreshTokenRepository;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Agent\Agent;

/**
 * class AuthorizeRefreshTokenService
 * @package App\Services\Base
 */
class AuthRefreshTokenService
{

    private static ?string $refreshToken;

    private Agent $agent;

    public AuthRefreshTokenRepository $repository;

    public function __construct()
    {
        $this->agent = resolve(Agent::class);
        $this->repository = resolve(AuthRefreshTokenRepository::class);
    }

    /**
     * @param string $accessToken
     * @return void
     */
    public static function encodeRefreshToken(string $accessToken): void
    {
        self::$refreshToken = GenerateAuthRefreshToken::encode($accessToken);
    }

    /**
     * @param string $refreshToken
     * @return string
     */
    public static function decodeRefreshToken(string $refreshToken): string
    {
        return GenerateAuthRefreshToken::decode($refreshToken);
    }

    /**
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        return self::$refreshToken;
    }

    /**
     * @param User $user
     * @param string $accessToken
     * @param string $deviceId
     * @param string $ipAddress
     * @return void
     */
    public function registerRefreshToken(User $user, string $accessToken, string $deviceId, string $ipAddress): void
    {
        $this->encodeRefreshToken($accessToken);
        $data = $this->getRefreshTokenData($user->user_id, $deviceId, $ipAddress);
        $this->repository->saveRefreshTokenBy('device_id', $deviceId, $data);
    }

    /**
     * @param User $user
     * @param string|null $deviceId
     * @return void
     */
    public function deleteRefreshToken(User $user, string $deviceId): void
    {
        $foundUserRefreshToken = $this->repository->findDeviceIdByUserId($user->user_id, $deviceId);
        is_null($foundUserRefreshToken) ?: $foundUserRefreshToken->delete();
    }

    /**
     * @param int $userId
     * @param string $deviceId
     * @param string|null $ipAddress
     * @return array
     */
    private function getRefreshTokenData(int $userId, string $deviceId, string $ipAddress = null): array
    {
        $data = [
            'user_id' => $userId,
            'refresh_token' => self::$refreshToken,
            'user_agent' => $this->agent->getUserAgent(),
            'device_id' => $deviceId,
            'device' => $this->agent->device(),
            'device_type' => $this->agent->deviceType(),
            'platform' => $platform = $this->agent->platform(),
            'platform_version' => $this->agent->version($platform),
            'browser' => $browser = $this->agent->browser(),
            'browser_version' => $this->agent->version($browser),
            'expire' => now()->addMinutes(config('auth-refresh-token.ttl'))
        ];

        if (!is_null($ipAddress)) {
            $data['ip_address'] = $ipAddress;
        }

        return $data;
    }

    /**
     * @param string $refreshToken
     * @param string $deviceId
     * @return Model
     * @throws RefreshTokenExpiredException
     * @throws RefreshTokenNotFoundException
     */
    public function refresh(string $refreshToken, string $deviceId): Model
    {
        /** @var AuthRefreshToken|null $foundUserRefreshToken */
        $foundUserRefreshToken = $this->repository->findRefreshTokenBy('device_id', $deviceId, $refreshToken);

        if (is_null($foundUserRefreshToken)) {
            throw new RefreshTokenNotFoundException();
        }

        if ($foundUserRefreshToken->expire < now()->toIso8601String()) {
            $foundUserRefreshToken->delete();
            throw new RefreshTokenExpiredException();
        }

        self::invalidateAccessToken($refreshToken);

        return $foundUserRefreshToken->user;
    }

    /**
     * @param User $user
     * @param string $refreshToken
     * @param string $deviceId
     * @return void
     */
    public function saveRefreshToken(User $user, string $refreshToken, string $deviceId): void
    {
        $this->repository->saveRefreshTokenBy(
            'refresh_token',
            $refreshToken,
            $this->getRefreshTokenData($user->user_id, $deviceId)
        );
    }

    /**
     * @param string $refreshToken
     * @return bool
     */
    public static function invalidateAccessToken(string $refreshToken): bool
    {
        $decodedRefreshToken = self::decodeRefreshToken($refreshToken);

        return AuthorizeService::invalidateAccessToken($decodedRefreshToken);
    }
}
