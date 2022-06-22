<?php

declare(strict_types=1);

namespace App\Listeners\Follow;

use App\Events\Follow\FollowedEvent;
use App\Facades\Notify;
use App\Handlers\User\Settings\UserCheckNotificationsSettingsHandler;
use App\Notifications\Follow\FollowNotification;
use App\Repositories\UserRepository;

/**
 * class FollowedListener
 * @package App\Listeners\Follow
 */
class FollowedListener
{
    private UserRepository $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param FollowedEvent $followedEvent
     * @return void
     */
    public function handle(FollowedEvent $followedEvent)
    {
        $recipient = $this->userRepository->getUserNotificationsSettings($followedEvent->follower->following_id);

        if (!UserCheckNotificationsSettingsHandler::execute($followedEvent->follower->follower_id, $recipient)) {
            return;
        }

        $data = [
            'url' => "/{$followedEvent->followerUsername}"
        ];

         Notify::store($followedEvent->follower->follower_id, $recipient->user_id, FollowNotification::TYPE, $data);
    }
}
