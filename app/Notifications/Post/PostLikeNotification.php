<?php

declare(strict_types=1);

namespace App\Notifications\Post;

use App\Models\User;
use App\Notifications\Contracts\BaseFCMNotification;
use Illuminate\Bus\Queueable;

/**
 * class PostLikeNotification
 * @package App\Notifications\Post
 */
class PostLikeNotification extends BaseFCMNotification
{
    use Queueable;

    public const TYPE = 'feed_post_like';

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
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
            'subtitle' => 'liked your post',
            'body' => $this->notification->post->postText,
            'post_id' => $this->notification->post_id
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
