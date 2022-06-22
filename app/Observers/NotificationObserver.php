<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Notification;
use App\Notifications\Blog\Comment\BlogCommentLikeNotification;
use App\Notifications\Blog\CommentReply\BlogCommentReplyLikeNotification;
use App\Notifications\Blog\CommentReply\BlogCreatedCommentReplyNotification;
use App\Notifications\Follow\FollowNotification;
use App\Notifications\Post\Comment\PostCommentCreatedNotification;
use App\Notifications\Post\Comment\PostCommentLikeNotification;
use App\Notifications\Post\CommentReply\PostCommentReplyLikeNotification;
use App\Notifications\Post\CommentReply\PostCommentReplyCreatedNotification;
use App\Notifications\Post\PostLikeNotification;
use App\Notifications\Post\PostRepostedNotification;
use Illuminate\Pipeline\Pipeline;

/**
 * class NotificationObserver
 * @package App\Observers
 */
class NotificationObserver
{

    /**
     * @var array|string[] $notifications
     */
    private array $notifications = [
        FollowNotification::class,
        BlogCommentLikeNotification::class,
        BlogCreatedCommentReplyNotification::class,
        BlogCommentReplyLikeNotification::class,
        PostRepostedNotification::class,
        PostLikeNotification::class,
        PostCommentCreatedNotification::class,
        PostCommentReplyCreatedNotification::class,
        PostCommentCreatedNotification::class,
        PostCommentLikeNotification::class,
        PostCommentReplyLikeNotification::class
    ];

    /**
     * @var Pipeline $pipeline
     */
    private Pipeline $pipeline;

    public function __construct(Pipeline $pipeline)
    {
        $this->pipeline = $pipeline;
    }

    /**
     * @param Notification $notification
     */
    public function creating(Notification $notification): void
    {
        $notification->setAttribute('time', time());
    }

    /**
     * Handle the Notification "created" event.
     *
     * @param  \App\Models\Notification  $notification
     * @return void
     */
    public function created(Notification $notification): void
    {
        $this->pipeline->send($notification)
                        ->through($this->notifications)
                        ->thenReturn();
    }

    /**
     * Handle the Notification "updated" event.
     *
     * @param  \App\Models\Notification  $notification
     * @return void
     */
    public function updated(Notification $notification): void
    {
        //
    }

    /**
     * Handle the Notification "deleted" event.
     *
     * @param  \App\Models\Notification  $notification
     * @return void
     */
    public function deleted(Notification $notification): void
    {
        //
    }

    /**
     * Handle the Notification "restored" event.
     *
     * @param  \App\Models\Notification  $notification
     * @return void
     */
    public function restored(Notification $notification): void
    {
        //
    }

    /**
     * Handle the Notification "force deleted" event.
     *
     * @param  \App\Models\Notification  $notification
     * @return void
     */
    public function forceDeleted(Notification $notification): void
    {
        //
    }
}
