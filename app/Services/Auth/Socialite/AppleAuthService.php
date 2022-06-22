<?php

declare(strict_types=1);

namespace App\Services\Auth\Socialite;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Manager\OAuth2\User as OAuthTwoUser;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * class AppleAuthService
 * @package App\Services\Auth\Socialite
 */
class AppleAuthService
{
    /**
     * @var UserRepository $userRepository
     */
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $token
     * @param string $deviceId
     * @param array $data
     * @param string $ipAddress
     * @return array
     * @throws \Exception
     */
    public function handle(string $token, string $deviceId, array $data, string $ipAddress): array
    {
        /** @var OAuthTwoUser $appleUserData */
        $appleUserData = Socialite::driver('apple')->userFromToken($token);

        if ($appleUserData) {
            if ($foundUserByAppleId = $this->userRepository->findByOrNull('apple', $appleUserData->getId())) {
                /** @var User $foundUserByAppleId */
                return $this->loginUser($foundUserByAppleId, $deviceId, $ipAddress);
            } else if ($foundUserByAppleEmail = $this->userRepository->findByOrNull('email', $appleUserData->getEmail())) {
                /** @var User $foundUserByAppleEmail */
                return $this->loginUser($foundUserByAppleEmail, $deviceId, $ipAddress);
            } else {
                $data = $this->getAppleRegisterUserData($appleUserData, $data);
                $appleRegisteredUser = $this->appleRegisteredUser($data);

                return $this->loginUser($appleRegisteredUser, $deviceId, $ipAddress);
            }
        } else {
            throw new BadRequestException('Invalid apple token');
        }
    }

    /**
     * @param User $user
     * @param string $deviceId
     * @param string $ipAddress
     * @return array
     */
    private function loginUser(User $user, string $deviceId, string $ipAddress): array
    {
        return app()->call(UserService::class . '@auth', compact('user', 'deviceId', 'ipAddress'));
    }

    /**
     * @param array $appleUserData
     * @return User
     * @throws \Exception
     */
    private function appleRegisteredUser(array $appleUserData): User
    {
        foreach (['first_name', 'last_name'] as $value) {
            $appleUserData[$value] = array_key_exists($value, $appleUserData) ? $appleUserData[$value] : 'Unknown';
            $appleUserData['username'] = array_key_exists('username', $appleUserData) ? $appleUserData['username'] . $appleUserData[$value] : $appleUserData[$value];
        }

        /** @var User $googleRegisteredUsered */
        $appleRegisteredUser = $this->userRepository->create([
            'first_name' => $appleUserData['first_name'],
            'last_name' => $appleUserData['last_name'],
            'username' => Str::snake($appleUserData['username']) . random_int(10, 100),
            'email' => $appleUserData['email'],
            'apple' => $appleUserData['id'],
            'password' => Str::random(),
            'active' => User::USER_STATUS_ACTIVE,
        ]);

        return $appleRegisteredUser;
    }

    /**
     * @param OAuthTwoUser $appleUserData
     * @param array $data
     * @return array
     */
    private function getAppleRegisterUserData(OAuthTwoUser $appleUserData, array $data): array
    {
        $appleUserDataArr = (array) $appleUserData;

        if (array_key_exists('user', $appleUserDataArr)) {
            foreach ($appleUserDataArr['user'] as $key => $value) {
                $appleUserDataArr[$key] = $value;
            }
            unset($appleUserDataArr['user']);
        }

        return array_filter(array_merge($appleUserDataArr, $data));
    }
}
