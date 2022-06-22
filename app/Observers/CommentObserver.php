<?php

declare(strict_types=1);

namespace App\Observers;

use App\Facades\Notify;
use App\Models\Comment;
use App\Notifications\Post\Comment\PostCommentCreatedNotification;

/**
 * class CommentObserver
 * @package App\Observers
 */
class CommentObserver
{

    /**
     * @param Comment $comment
     */
    public function creating(Comment $comment): void
    {
        $comment->setAttribute('time', time());
    }

    /**
     * Handle the Comment "created" event.
     *
     * @param  \App\Models\Comment  $comment
     * @return void
     */
    public function created(Comment $comment): void
    {
        //
    }

    /**
     * Handle the Comment "updated" event.
     *
     * @param  \App\Models\Comment  $comment
     * @return void
     */
    public function updated(Comment $comment): void
    {
        //
    }

    /**
     * Handle the Comment "deleted" event.
     *
     * @param  \App\Models\Comment  $comment
     * @return void
     */
    public function deleted(Comment $comment): void
    {
        //
    }

    /**
     * Handle the Comment "restored" event.
     *
     * @param  \App\Models\Comment  $comment
     * @return void
     */
    public function restored(Comment $comment): void
    {
        //
    }

    /**
     * Handle the Comment "force deleted" event.
     *
     * @param  \App\Models\Comment  $comment
     * @return void
     */
    public function forceDeleted(Comment $comment): void
    {
        //
    }
}
