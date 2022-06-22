<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Header\ClientHeaderInfoDTO;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * class GoogleAuthService
 * @package App\Services
 */
class GoogleAuthService
{
    use ApiResponseTrait;

    private User $user;

    /**
     * @var UserRepository $userRepository
     */
    private UserRepository $userRepository;

    private \Google_Client $googleClient;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->googleClient = app()->make('google_client');
    }

    /**
     * @param string $googleToken
     * @param string $deviceId
     * @param string $ipAddress
     * @param ClientHeaderInfoDTO $clientHeaderInfoDTO
     * @return array
     * @throws \Exception
     */
    public function handle(string $googleToken, string $deviceId, string $ipAddress, ClientHeaderInfoDTO $clientHeaderInfoDTO): array
    {
        if ($clientHeaderInfoDTO->os == ClientHeaderInfoDTO::PREFIX_OS_IOS) {
            $googleUserData = Socialite::driver('google')->userFromToken($googleToken);
        } else {
            $googleUserData = $this->googleClient->verifyIdToken($googleToken);
        }

        if ($googleUserData) {
            if ($foundUserByGoogleId = $this->userRepository->findByOrNull('google', $googleUserData['sub'])) {
                /** @var User $foundUserByGoogleId */
                return $this->loginUser($foundUserByGoogleId, $deviceId, $ipAddress);
            } else if ($foundUserByGoogleEmail = $this->userRepository->findByOrNull('email', $googleUserData['email'])) {
                /** @var User $foundUserByGoogleEmail */
                return $this->loginUser($foundUserByGoogleEmail, $deviceId, $ipAddress);
            } else {
                $googleRegisteredUser = $this->googleRegisteredUser($googleUserData);

                return $this->loginUser($googleRegisteredUser, $deviceId, $ipAddress);
            }
        } else {
            throw new BadRequestException('Invalid google token');
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
     * @param array $googleUserData
     * @return User
     * @throws \Exception
     */
    private function googleRegisteredUser(array $googleUserData): User
    {
        /** @var User $googleRegisteredUsered */
        $googleRegisteredUser = $this->userRepository->create([
            'first_name' => $googleUserData['given_name'],
            'last_name' => $googleUserData['family_name'],
            'username' => Str::snake($googleUserData['name']) . random_int(10, 100),
            'email' => $googleUserData['email'],
            'google' => $googleUserData['sub'],
            'password' => Str::random(),
            'active' => \App\Models\User::USER_STATUS_ACTIVE,
        ]);

        return $googleRegisteredUser;
    }
}
