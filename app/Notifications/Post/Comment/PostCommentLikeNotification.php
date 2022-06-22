<?php

declare(strict_types=1);

namespace App\Notifications\Post\Comment;

use App\Models\User;
use App\Notifications\Contracts\BaseFCMNotification;
use Illuminate\Bus\Queueable;

/**
 * class PostCommentLikeNotification
 * @package App\Notifications\Post\Comment
 */
class PostCommentLikeNotification extends BaseFCMNotification
{
    use Queueable;

    public const TYPE = 'feed_post_comment_like';

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
        /** @var User $notifier */
        $notifier = $this->notification->notifier;

        return [
            'title' => ($this->countUnreadNotificationsByType() > 0) ? "{$notifier->full_name} & {$this->countUnreadNotificationsByType()} other" : $notifier->full_name,
            'subtitle' => 'liked on your comment',
            'body' => $this->notification->comment->text,
            'content_id' => $this->notification->post_id,
            'comment_id' => $this->notification->comment_id
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
