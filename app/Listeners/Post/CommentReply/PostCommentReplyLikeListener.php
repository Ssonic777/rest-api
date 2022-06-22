<?php

declare(strict_types=1);

namespace App\Listeners\Post\CommentReply;

use App\Events\Post\CommentReply\PostCommentReplyLikeEvent;
use App\Facades\Notify;
use App\Handlers\User\Settings\UserCheckNotificationsSettingsHandler;
use App\Models\User;
use App\Notifications\Post\CommentReply\PostCommentReplyLikeNotification;
use App\Repositories\UserRepository;

/**
 * class PostCommentReplyLikeListener
 * @package App\Listeners\Post\CommentReply
 */
class PostCommentReplyLikeListener
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
     * @param  PostCommentReplyLikeEvent  $event
     * @return void
     */
    public function handle(PostCommentReplyLikeEvent $event): void
    {
        $recipient = $this->userRepository->getUserNotificationsSettings($event->commentReply->user_id);

        if (!UserCheckNotificationsSettingsHandler::execute(auth()->guard('api')->id(), $recipient)) {
            return;
        }

        $data = [
            'post_id' => $event->commentReply->comment->post_id,
            'comment_id' => $event->commentReply->comment_id,
            'reply_id' => $event->commentReply->id
        ];

        Notify::store(
            auth()->guard('api')->id(),
            $recipient->user_id,
            PostCommentReplyLikeNotification::TYPE,
            $data
        );
    }
}
