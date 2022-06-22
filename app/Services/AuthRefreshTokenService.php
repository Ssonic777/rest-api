<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\AuthRefreshTokenRepository;

/**
 * class AuthRefreshTokenService
 * @package App\Services
 */
class AuthRefreshTokenService
{
    /**
     * @var AuthRefreshTokenRepository $repository
     */
    private AuthRefreshTokenRepository $repository;

    public function __construct(AuthRefreshTokenRepository $repository)
    {
        $this->repository = $repository;
    }
}
