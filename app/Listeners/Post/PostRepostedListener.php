<?php

declare(strict_types=1);

namespace App\Listeners\Post;

use App\Events\Post\PostRepostedEvent;
use App\Facades\Notify;
use App\Handlers\User\Settings\UserCheckNotificationsSettingsHandler;
use App\Notifications\Post\PostRepostedNotification;
use App\Repositories\UserRepository;

/**
 * class PostReportedListener
 * @package App\Listeners\Post
 */
class PostRepostedListener
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
     * Handle the event.
     *
     * @param  PostRepostedEvent $event
     * @return void
     */
    public function handle(PostRepostedEvent $event): void
    {
        $recipient = $this->userRepository->getUserNotificationsSettings($event->reportedPost->user_id);

        if (!UserCheckNotificationsSettingsHandler::execute($event->reportedPost->parent->user_id, $recipient)) {
            return;
        }

        $data = [
            'post_id' => $event->reportedPost->parent->post_id,
            'text' => $event->reportedPost->parent->postText
        ];

        Notify::store($event->reportedPost->parent->user_id, $recipient->user_id, PostRepostedNotification::TYPE, $data);
    }
}
