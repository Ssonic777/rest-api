<?php

declare(strict_types=1);

namespace App\Listeners\Post\CommentReply;

use App\Events\PostCommentReplyCreatedEvent;
use App\Facades\Notify;
use App\Handlers\User\Settings\UserCheckNotificationsSettingsHandler;
use App\Models\User;
use App\Notifications\Post\CommentReply\PostCommentReplyCreatedNotification;
use App\Repositories\UserRepository;

/**
 * class PostCommentReply
 * @package  App\Listeners\Post\CommentReply
 */
class PostCommentReplyCreatedListener
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
     * @var array $subtitles
     */
    private array $subtitles = [
        'post-user' => 'replied on your post comment',
        'comment-user' => 'replied to your comment'
    ];

    /**
     * Handle the event.
     *
     * @param  PostCommentReplyCreatedEvent  $event
     * @return void
     */
    public function handle(PostCommentReplyCreatedEvent $event): void
    {
        if ($event->commentReply->comment->post->enable_notifications) {
            $recipients = [
                'post-user' => $event->commentReply->comment->post->user_id,
                'comment-user' => $event->commentReply->comment->user_id,
            ];

            $data = [
                'post_id' => $event->commentReply->comment->post_id,
                'comment_id' => $event->commentReply->comment_id,
                'reply_id' => $event->commentReply->id,
            ];

            foreach ($recipients as $key => $recipientId) {
                $recipient = $this->userRepository->getUserNotificationsSettings($recipientId);

                if (!UserCheckNotificationsSettingsHandler::execute($event->commentReply->user_id, $recipient)) {
                    continue;
                }

                Notify::store(
                    $event->commentReply->user_id,
                    $recipient->user_id,
                    PostCommentReplyCreatedNotification::TYPE,
                    array_merge($data, ['text' => $this->subtitles[$key]])
                );
            }
        }
    }
}
