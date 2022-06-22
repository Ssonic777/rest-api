<?php

declare(strict_types=1);

namespace App\Notifications\Post;

use App\Models\User;
use App\Notifications\Contracts\BaseFCMNotification;
use Illuminate\Bus\Queueable;

/**
 * class PostReportedNotification
 * @package App\Notifications\Post
 */
class PostRepostedNotification extends BaseFCMNotification
{
    use Queueable;

    public const TYPE = 'repost_post';

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

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
            'subtitle' => 'reported your post',
            'body' => $this->notification->post->postText,
            'post_id' => $this->notification->post_id
        ];
    }
}
