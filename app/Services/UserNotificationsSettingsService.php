<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

/**
 * class UserNotificationsSettingsService
 * @package App\Services
 */
class UserNotificationsSettingsService
{
    private UserRepository $repository;

    /**
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getUserNotificationsSettings(int $userId): array
    {
        $user = $this->repository->getUserNotificationsSettings($userId);

        return $this->prepareResponse($user);
    }

    /**
     * @param int $userId
     * @param array $data
     * @return array
     */
    public function setUserNotificationsSettings(int $userId, array $data): array
    {
        ['allow' => $allow, 'from' => $from, 'sounds' => $sounds] = $data;

        $user = $this->repository->getUserNotificationsSettings($userId);

        $user->update([
            'notifications_allow' => User::NOTIFICATIONS_ALLOW[$allow],
            'notifications_from' => User::NOTIFICATIONS_ALLOW_FROM[$from],
            'notifications_sound' => User::NOTIFICATIONS_SOUNDS[$sounds],
        ]);

        return $this->prepareResponse($user);
    }

    /**
     * @param User $user
     * @return array
     */
    public function prepareResponse(User $user): array
    {
        return [
            'allow' => array_search($user->notifications_allow, User::NOTIFICATIONS_ALLOW),
            'from' => array_search($user->notifications_from, User::NOTIFICATIONS_ALLOW_FROM),
            'sounds' => array_search($user->notifications_sound, User::NOTIFICATIONS_SOUNDS),
        ];
    }
}
