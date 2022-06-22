<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Models\AuthRefreshToken;
use Carbon\Carbon;

/**
 * class AuthSessionAttributesHandler
 * @package App\Handlers
 */
class AuthSessionAttributesHandler
{
    /**
     * @var int|\Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed $accessTokenTTL
     */
    private int $accessTokenTTL;

    /**
     * @var int $accessTokenDefaultTTL
     */
    private int $accessTokenDefaultTTL = 15;

    /**
     * @var Carbon $carbon
     */
    private Carbon $carbon;

    public function __construct(Carbon $carbon)
    {
        $this->carbon = $carbon;
        $this->accessTokenTTL = config('jwt.ttl', $this->accessTokenDefaultTTL);
    }

    /**
     * @param AuthRefreshToken $authRefreshToken
     * @return AuthRefreshToken
     */
    public function execute(AuthRefreshToken $authRefreshToken): AuthRefreshToken
    {
        $this->setIsActiveAttribute($authRefreshToken);

        return $authRefreshToken;
    }

    /**
     * @param AuthRefreshToken $model
     * @return void
     */
    private function setIsActiveAttribute(AuthRefreshToken $model): void
    {
        $currentTime = $this->carbon::now();
        $lastUpdatedRefreshTokenTime = $model->getRawOriginal('updated_at');
        $isActive = $lastUpdatedRefreshTokenTime > $currentTime->subMinutes($this->accessTokenTTL)->format('Y-m-d H:i:s');
        $model->setAttribute('is_active', $isActive);
    }
}
