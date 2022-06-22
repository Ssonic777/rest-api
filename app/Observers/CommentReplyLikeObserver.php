<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\CommentReplyLike;

/**
 * class CommentReplyLikeObserver
 * @package App\Observers
 */
class CommentReplyLikeObserver
{
    /**
     * Handle the CommentReplyLike "created" event.
     *
     * @param  \App\Models\CommentReplyLike  $commentReplyLike
     * @return void
     */
    public function created(CommentReplyLike $commentReplyLike): void
    {
        //
    }

    /**
     * Handle the CommentReplyLike "updated" event.
     *
     * @param  \App\Models\CommentReplyLike  $commentReplyLike
     * @return void
     */
    public function updated(CommentReplyLike $commentReplyLike): void
    {
        //
    }

    /**
     * Handle the CommentReplyLike "deleted" event.
     *
     * @param  \App\Models\CommentReplyLike  $commentReplyLike
     * @return void
     */
    public function deleted(CommentReplyLike $commentReplyLike): void
    {
    }

    /**
     * Handle the CommentReplyLike "restored" event.
     *
     * @param  \App\Models\CommentReplyLike  $commentReplyLike
     * @return void
     */
    public function restored(CommentReplyLike $commentReplyLike): void
    {
        //
    }

    /**
     * Handle the CommentReplyLike "force deleted" event.
     *
     * @param  \App\Models\CommentReplyLike  $commentReplyLike
     * @return void
     */
    public function forceDeleted(CommentReplyLike $commentReplyLike): void
    {
        //
    }
}
