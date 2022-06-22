<?php

declare(strict_types=1);

namespace App\Listeners\Post\Comment;

use App\Events\Post\Comment\PostCommentCreatedEvent;
use App\Handlers\User\Settings\UserCheckNotificationsSettingsHandler;
use App\Models\User;
use App\Notifications\Post\Comment\PostCommentCreatedNotification;
use App\Facades\Notify;
use App\Repositories\UserRepository;

/**
 * class PostCommentCreatedListener
 * @package App\Listeners\Post\Comment
 */
class PostCommentCreatedListener
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
     * @param  PostCommentCreatedEvent  $event
     * @return void
     */
    public function handle(PostCommentCreatedEvent $event): void
    {
        if ($event->comment->post->enable_notifications) {
            $recipient = $this->userRepository->getUserNotificationsSettings($event->comment->post->user_id);

            if (!UserCheckNotificationsSettingsHandler::execute($event->comment->user_id, $recipient)) {
                return;
            }

            $data = [
                'post_id' => $event->comment->post_id,
                'comment_id' => $event->comment->id,
            ];

            Notify::store($event->comment->user_id, $recipient->user_id, PostCommentCreatedNotification::TYPE, $data);
        }
    }
}
