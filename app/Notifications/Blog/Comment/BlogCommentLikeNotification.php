<?php

declare(strict_types=1);

namespace App\Notifications\Blog\Comment;

use App\Models\User;
use App\Notifications\Contracts\BaseFCMNotification;
use Illuminate\Bus\Queueable;

/**
 * class BlogCommentLikeNotification
 * @package App\Notifications\Blog
 */
class BlogCommentLikeNotification extends BaseFCMNotification
{
    use Queueable;

    public const TYPE = 'blockdesk_article_comment_like';

    /**
     * @var User|null $authUser
     */
    private ?User $authUser;

    public function __construct()
    {
        $this->authUser = auth()->guard('api')->user();
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
        return [
            'title' => ($this->countUnreadNotificationsByType()) ? "{$this->authUser->full_name} & {$this->countUnreadNotificationsByType()} other" : $this->authUser->full_name,
            'subtitle' => 'liked your comment',
            'body' => $this->notification->comment->text,
            'content_id' => $this->notification->blog_id,
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
