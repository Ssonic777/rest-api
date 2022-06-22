<?php

declare(strict_types=1);

namespace App\Listeners\Blog\CommentReply;

use App\Events\Blog\CommentReply\BlogCommentReplyCreatedEvent;
use App\Facades\Notify;
use App\Handlers\User\Settings\UserCheckNotificationsSettingsHandler;
use App\Models\User;
use App\Notifications\Blog\CommentReply\BlogCreatedCommentReplyNotification;
use App\Repositories\UserRepository;

/**
 * class BlogCommentReplyCreatedListener
 * @package App\Listeners\Blog\CommentReply
 */
class BlogCommentReplyCreatedListener
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
     * @param  BlogCommentReplyCreatedEvent  $event
     * @return void
     */
    public function handle(BlogCommentReplyCreatedEvent $event): void
    {
        $recipient = $this->userRepository->getUserNotificationsSettings($event->commentReply->comment->user_id);

        if (!UserCheckNotificationsSettingsHandler::execute($event->commentReply->user->user_id, $recipient)) {
            return;
        }

        $data = [
            'blog_id' => $event->commentReply->comment->post->blog_id,
            'comment_id' => $event->commentReply->comment->id,
            'reply_id' => $event->commentReply->id
        ];

        Notify::store(
            $event->commentReply->user->user_id,
            $recipient->user_id,
            BlogCreatedCommentReplyNotification::TYPE,
            $data
        );
    }
}
