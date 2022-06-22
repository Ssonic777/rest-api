<?php

declare(strict_types=1);

namespace App\Listeners\Blog\CommentReply;

use App\Events\Blog\CommentReply\BlogCommentReplyLikedEvent;
use App\Facades\Notify;
use App\Handlers\User\Settings\UserCheckNotificationsSettingsHandler;
use App\Models\User;
use App\Notifications\Blog\CommentReply\BlogCommentReplyLikeNotification;
use App\Repositories\UserRepository;

/**
 * class BlogCommentReplyLikedListener
 * @package
 */
class BlogCommentReplyLikedListener
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
     * @param BlogCommentReplyLikedEvent $blogCommentReplyLikedEvent
     * @return void
     */
    public function handle(BlogCommentReplyLikedEvent $blogCommentReplyLikedEvent): void
    {
        $recipient = $this->userRepository->getUserNotificationsSettings($blogCommentReplyLikedEvent->commentReply->user_id);

        if (!UserCheckNotificationsSettingsHandler::execute($blogCommentReplyLikedEvent->likedUserId, $recipient)) {
            return;
        }

        $data = [
            'blog_id' => $blogCommentReplyLikedEvent->commentReply->comment->post_id,
            'comment_id' => $blogCommentReplyLikedEvent->commentReply->comment_id,
            'reply_id' => $blogCommentReplyLikedEvent->commentReply->id,
        ];

        Notify::store($blogCommentReplyLikedEvent->likedUserId, $recipient->user_id, BlogCommentReplyLikeNotification::TYPE, $data);
    }
}
