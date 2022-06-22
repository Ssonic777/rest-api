<?php

declare(strict_types=1);

namespace App\Notifications\Post\CommentReply;

use App\Models\User;
use App\Notifications\Contracts\BaseFCMNotification;
use Illuminate\Bus\Queueable;

/**
 * class PostCommentReplyCreatedNotification
 * @package App\Notifications\Post\CommentReply
 */
class PostCommentReplyCreatedNotification extends BaseFCMNotification
{
    use Queueable;

    public const TYPE = 'feed_post_new_comment_reply';

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['firebase'];
    }

    /**
     * @param User $notifiable
     * @return array
     */
    public function toFCM(User $notifiable): array
    {
        $notifier = $this->notification->notifier;

        return [
            'title' => ($this->countUnreadNotificationsByType() > 0) ? "{$notifier->full_name} & {$this->countUnreadNotificationsByType()} other" : $notifier->full_name,
            'subtitle' => $this->notification->text,
            'body' => $this->notification->comment->text,
            'content_id' => $this->notification->comment->post_id,
            'comment_id' => $this->notification->id,
            'comment_reply_id' => $this->notification->reply_id
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
