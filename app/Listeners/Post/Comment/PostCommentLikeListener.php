<?php

declare(strict_types=1);

namespace App\Listeners\Post\Comment;

use App\Events\Post\Comment\PostCommentLikeEvent;
use App\Facades\Notify;
use App\Handlers\User\Settings\UserCheckNotificationsSettingsHandler;
use App\Models\User;
use App\Notifications\Post\Comment\PostCommentLikeNotification;
use App\Repositories\UserRepository;

/**
 * class PostCommentLikeListener
 * @package App\Listeners\Post\Comment
 */
class PostCommentLikeListener
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
     * @param  PostCommentLikeEvent  $event
     * @return void
     */
    public function handle(PostCommentLikeEvent $event): void
    {
        if ($event->comment->post->enable_notifications) {
            $recipient = $this->userRepository->getUserNotificationsSettings($event->comment->user_id);

            if (!UserCheckNotificationsSettingsHandler::execute(auth()->guard('api')->id(), $recipient)) {
                return;
            }

            $data = [
                'post_id' => $event->comment->post_id,
                'comment_id' => $event->comment->id
            ];

            Notify::store(auth()->guard('api')->id(), $recipient->user_id, PostCommentLikeNotification::TYPE, $data);
        }
    }
}
