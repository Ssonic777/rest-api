<?php

declare(strict_types=1);

namespace App\Listeners\Blog\Comment;

use App\Events\Blog\Comment\BlogCommentLikedEvent;
use App\Facades\Notify;
use App\Handlers\User\Settings\UserCheckNotificationsSettingsHandler;
use App\Models\User;
use App\Notifications\Blog\Comment\BlogCommentLikeNotification;
use App\Repositories\UserRepository;

/**
 * class BlogCommentCreatedListener
 * @package App\Listeners\Blog\Comment
 */
class BlogCommentLikedListener
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
     * @param BlogCommentLikedEvent $event
     * @return void
     */
    public function handle(BlogCommentLikedEvent $event): void
    {
        $recipient = $this->userRepository->getUserNotificationsSettings($event->comment->user_id);

        if (!UserCheckNotificationsSettingsHandler::execute($event->likedUserId, $recipient)) {
            return;
        }

        $data = [
            'comment_id' => $event->comment->id,
            'blog_id' => $event->comment->post->blog_id
        ];

        Notify::store($event->likedUserId, $recipient->user_id, BlogCommentLikeNotification::TYPE, $data);
    }
}
