<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\BlogCommentReply;

/**
 * class BlogCommentReplyObserver
 * @package App\Observer
 */
class BlogCommentReplyObserver
{

    /**
     * @param BlogCommentReply $blogCommentReply
     */
    public function creating(BlogCommentReply $blogCommentReply): void
    {
        $blogCommentReply->setAttribute('posted', time());
    }

    /**
     * Handle the BlogCommentReply "created" event.
     *
     * @param  \App\Models\BlogCommentReply  $blogCommentReply
     * @return void
     */
    public function created(BlogCommentReply $blogCommentReply): void
    {
        //
    }

    /**
     * Handle the BlogCommentReply "updated" event.
     *
     * @param  \App\Models\BlogCommentReply  $blogCommentReply
     * @return void
     */
    public function updated(BlogCommentReply $blogCommentReply): void
    {
        //
    }

    /**
     * Handle the BlogCommentReply "deleted" event.
     *
     * @param  \App\Models\BlogCommentReply  $blogCommentReply
     * @return void
     */
    public function deleted(BlogCommentReply $blogCommentReply): void
    {
        //
    }

    /**
     * Handle the BlogCommentReply "restored" event.
     *
     * @param  \App\Models\BlogCommentReply  $blogCommentReply
     * @return void
     */
    public function restored(BlogCommentReply $blogCommentReply): void
    {
        //
    }

    /**
     * Handle the BlogCommentReply "force deleted" event.
     *
     * @param  \App\Models\BlogCommentReply  $blogCommentReply
     * @return void
     */
    public function forceDeleted(BlogCommentReply $blogCommentReply): void
    {
        //
    }
}
