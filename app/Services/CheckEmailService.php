<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * class CheckEmailService
 * @package App\Services
 */
class CheckEmailService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param array $data
     * @return array
     */
    public function checkEmail(array $data): array
    {
        if (array_key_exists('email', $data)) {
            throw new BadRequestException("Parameter email is missing");
        }

        $checkEmail = $this->userRepository->checkEmail($data['email']);

        return [
            'message' => 'Done',
            'is_free' => $checkEmail
        ];
    }

    /**
     * @param array $data
     * @return string[]
     */
    public function checkEmailCode(array $data): array
    {
        $foundUser = $this->findUserForCheckEmailCode($data);

        if ((int) $foundUser->email_code !== (int) $data['email_code']) {
            throw new BadRequestException('Wrong email_code param');
        }

        return [
            'message' => 'Email code is valid',
        ];
    }

    /**
     * @param array $data
     * @return Model|User|null
     */
    private function findUserForCheckEmailCode(array $data): User
    {
        $cacheKey = "active-created-account-{$data['email_code']}";

        return Cache::has($cacheKey) ? Cache::get($cacheKey) : $this->userRepository->findBy('email', $data['email']);
    }
}
