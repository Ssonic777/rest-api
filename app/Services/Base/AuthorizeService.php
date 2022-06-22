<?php

declare(strict_types=1);

namespace App\Services\Base;

use App\Exceptions\Auth\RefreshTokenExpiredException;
use App\Exceptions\Auth\RefreshTokenNotFoundException;
use App\Exceptions\UserAlreadyActiveException;
use App\Mail\EmailVerification;
use App\Models\User;
use App\Repositories\FCMTokenRepository;
use App\Repositories\UserRepository;
use App\Services\Auth\UserCacheService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

/**
 * class AuthorizeService
 * @package App\Services\Base
 */
abstract class AuthorizeService
{
    use ApiResponseTrait;

    public UserRepository $repository;

    private AuthRefreshTokenService $authRefreshTokenService;

    public function __construct()
    {
        $this->initializeAuthorizeModelRepository();
    }

    abstract protected function getAuthorizeModelRepository(): string;

    private function initializeAuthorizeModelRepository(): void
    {
        $this->repository = resolve($this->getAuthorizeModelRepository());
        $this->authRefreshTokenService = resolve(AuthRefreshTokenService::class);
    }

    /**
     * @param array $data
     * @return array
     */
    public function register(array $data): array
    {
        /** @var User $registeredUser */
        $registeredUser = $this->repository->make(array_merge($data, [
            'username' => "{$data['first_name']}_{$data['last_name']}" . mt_rand(0, 1000),
            'email_code' => User::generateEmailCode(),
            'active' => User::USER_STATUS_NOT_ACTIVE,
        ]));

        UserCacheService::remember($registeredUser);

        Mail::to($registeredUser)->send(new EmailVerification($registeredUser));

        $response = [
            'message' => 'Need verify user',
            'error_code' => -1001
        ];

        return array_merge(
            $response,
            config('app.debug') ? ['email_code' => $registeredUser->email_code] : []
        );
    }

    /**
     * @param array $credentials
     * @param string $deviceId
     * @param string $ipAddress
     * @return array
     */
    public function login(array $credentials, string $deviceId, string $ipAddress): array
    {
        $foundUser = $this->repository->findByOrNull('email', $credentials['email']);

        if (is_null($foundUser)) {
            throw new BadRequestException('Incorrect data', 1001);
        }

        /** @var string $token */
        if (!$token = auth('api')->attempt($credentials)) {
            throw new BadRequestException('Incorrect data', 1001);
        }

        if ($foundUser instanceof User && $foundUser->isNotActive()) {
            throw new BadRequestException('Error verify user');
        }

        $this->authRefreshTokenService->registerRefreshToken($foundUser, $token, $deviceId, $ipAddress);

        return $this->generateAuthToken($token, $this->authRefreshTokenService->getRefreshToken());
    }

    /**
     * @param string $deviceId
     * @return void
     */
    public function logout(string $deviceId): void
    {
        $this->authRefreshTokenService->deleteRefreshToken(auth()->guard('api')->user(), $deviceId);
        app()->call(FCMTokenRepository::class . '@deleteByDeviceId', ['userId' => auth()->guard('api')->id(), 'deviceId' => $deviceId]);
        auth('api')->logout();
    }

    /**
     * @return Model
     */
    public function user(): Model
    {
        return auth('api')->user();
    }

    /**
     * @param string $refreshToken
     * @param string $deviceId
     * @return array
     * @throws RefreshTokenExpiredException
     * @throws RefreshTokenNotFoundException
     */
    public function refresh(string $refreshToken, string $deviceId): array
    {
        /** @var User|\Illuminate\Contracts\Auth\Authenticatable $foundUser */
        $foundUser = $this->authRefreshTokenService->refresh($refreshToken, $deviceId);

        /** @var string $token */
        $token = Auth::guard('api')->login($foundUser);
        $this->authRefreshTokenService->encodeRefreshToken($token);

        $this->authRefreshTokenService->saveRefreshToken($foundUser, $refreshToken, $deviceId);

        return $this->generateAuthToken($token, $this->authRefreshTokenService->getRefreshToken());
    }

    /**
     * @param string $email
     * @return array
     * @throws UserAlreadyActiveException
     */
    public function generateActiveToken(string $email): array
    {
        /** @var User $foundUser */
        $isFromCache = (bool) $foundUser = UserCacheService::findBy($email);

        if (!$isFromCache) {
            $foundUser = $this->repository->findBy('email', $email);
        }

        if ($foundUser->isActive()) {
            throw new UserAlreadyActiveException();
        }

        if (!$isFromCache) {
            $foundUser->email_code = User::generateEmailCode();
            $foundUser->save();
        } else {
            $foundUser->email_code = User::generateEmailCode();
            UserCacheService::remember($foundUser);
        }

        Mail::to($foundUser)->send(new EmailVerification($foundUser));

        $result = ['message' => 'Verification email was successfully sent'];

        return array_merge($result, config('app.debug') ? ['email_code' => $foundUser->email_code] : []);
    }

    /**
     * @param array $credentials
     * @param string $deviceId
     * @param string $ipAddress
     * @return array
     */
    public function storeNewPassword(array $credentials, string $deviceId, string $ipAddress): array
    {
        /** @var User $foundUser */
        $foundUser = $this->repository->findBy('email_code', $credentials['email_code']);
        $foundUser->update(array_merge($credentials, ['email_code' => '']));
        unset($credentials['email_code']);

        return $this->login(array_merge($credentials, ['email' => $foundUser->email]), $deviceId, $ipAddress);
    }

    /**
     * @param array $data
     * @param string $deviceId
     * @param string $ipAddress
     * @return array
     * @throws UserAlreadyActiveException
     */
    public function submitActive(array $data, string $deviceId, string $ipAddress): array
    {
        $foundUser = UserCacheService::findBy($data['email']) ?: $this->repository->findBy('email', $data['email']);

        if ($foundUser->email != $data['email']) {
            throw new BadRequestException('Wrong email');
        }

        if ($foundUser->email_code != $data['otp_code']) {
            throw new BadRequestException('Wrong validation_code');
        }

        if ($foundUser->active == User::USER_STATUS_ACTIVE) {
            throw new UserAlreadyActiveException();
        }

        $foundUser->activeAccount();

        /** @var string $token */
        $token = Auth::guard('api')->login($foundUser);
        $this->authRefreshTokenService->registerRefreshToken($foundUser, $token, $deviceId, $ipAddress);

        return $this->generateAuthToken($token, $this->authRefreshTokenService->getRefreshToken());
    }

    /**
     * @param array $data
     * @return array|null
     */
    public function generateResetPassword(array $data): array
    {
        /** @var User $foundUser */
        $foundUser = $this->repository->findBy('email', $data['email']);

        $foundUser->email_code = User::generateEmailCode();
        $foundUser->save();

        Mail::to($foundUser)->send(new EmailVerification($foundUser));

        $result = ['message' => 'Password change request was sent'];

        return array_merge($result, config('app.debug') ? ['email_code' => $foundUser->email_code] : []);
    }

    /**
     * @param User $user
     * @param string $deviceId
     * @param string $ipAddress
     * @return array
     */
    public function auth(User $user, string $deviceId, string $ipAddress): array
    {
        /** @var string $token */
        $token = Auth::guard('api')->login($user);
        $this->authRefreshTokenService->registerRefreshToken($user, $token, $deviceId, $ipAddress);

        return $this->generateAuthToken($token, $this->authRefreshTokenService->getRefreshToken());
    }

    /**
     * @param string $accessToken
     * @return bool
     */
    public static function invalidateAccessToken(string $accessToken): bool
    {
        $result = false;

        if (is_string($accessToken) && !is_null($accessToken)) {
            try {
                $result = app()->call('\Tymon\JWTAuth\Manager@invalidate', ['token' => new \Tymon\JWTAuth\Token($accessToken)]);
            } catch (TokenExpiredException $exception) {
                $result = true;
            }
        }

        return $result;
    }
}
