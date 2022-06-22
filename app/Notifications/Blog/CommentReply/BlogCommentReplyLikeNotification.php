<?php

declare(strict_types=1);

namespace App\Notifications\Blog\CommentReply;

use App\Models\User;
use App\Notifications\Contracts\BaseFCMNotification;
use Illuminate\Bus\Queueable;

/**
 * class BlogCommentReplyLikeNotification
 * @package App\Notifications\Blog\CommentReply
 */
class BlogCommentReplyLikeNotification extends BaseFCMNotification
{
    use Queueable;

    public const TYPE = 'blockdesk_article_reply_like';

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
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toFCM(User $notifiable): array
    {
        $notifier = $this->notification->notifier;

        return [
            'title' => ($this->countUnreadNotificationsByType()) ? $notifier->full_name . " & {$this->countUnreadNotificationsByType()} other" : $notifier->full_name,
            'subtitle' => 'liked your comment',
            'body' => $this->notification->reply->text,
            'content_id' => $this->notification->blog_id,
            'comment_id' => $this->notification->comment_id,
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
