<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\CommentReply;
use App\Models\User;

/**
 * class CommentReplyObserver
 * @package App\Observers
 */
class CommentReplyObserver
{

    /**
     * @param CommentReply $commentReply
     * @return void
     */
    public function creating(CommentReply $commentReply): void
    {
        $commentReply->setAttribute('time', time());
    }

    /**
     * Handle the CommentReply "created" event.
     *
     * @param  \App\Models\CommentReply  $commentReply
     * @return void
     */
    public function created(CommentReply $commentReply): void
    {
    }

    /**
     * @param CommentReply $commentReply
     * @return void
     */
    public function updating(CommentReply $commentReply): void
    {
        $commentReply->setAttribute('edited', time());
    }

    /**
     * Handle the CommentReply "updated" event.
     *
     * @param  \App\Models\CommentReply  $commentReply
     * @return void
     */
    public function updated(CommentReply $commentReply): void
    {
        //
    }

    /**
     * Handle the CommentReply "deleted" event.
     *
     * @param  \App\Models\CommentReply  $commentReply
     * @return void
     */
    public function deleted(CommentReply $commentReply): void
    {
        //
    }

    /**
     * Handle the CommentReply "restored" event.
     *
     * @param  \App\Models\CommentReply  $commentReply
     * @return void
     */
    public function restored(CommentReply $commentReply): void
    {
        //
    }

    /**
     * Handle the CommentReply "force deleted" event.
     *
     * @param  \App\Models\CommentReply  $commentReply
     * @return void
     */
    public function forceDeleted(CommentReply $commentReply): void
    {
        //
    }
}
