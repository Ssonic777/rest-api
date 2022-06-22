<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\BlogComment;

/**
 * class BlogCommentObserver
 * @package App\Models\BlogComment
 */
class BlogCommentObserver
{
    /**
     * @param BlogComment $blogComment
     */
    public function creating(BlogComment $blogComment): void
    {
        $blogComment->setAttribute('posted', time());
    }

    /**
     * Handle the BlogComment "created" event.
     *
     * @param  \App\Models\BlogComment  $blogComment
     * @return void
     */
    public function created(BlogComment $blogComment): void
    {
        //
    }

    /**
     * Handle the BlogComment "updated" event.
     *
     * @param  \App\Models\BlogComment  $blogComment
     * @return void
     */
    public function updated(BlogComment $blogComment): void
    {
        //
    }

    /**
     * Handle the BlogComment "deleted" event.
     *
     * @param  \App\Models\BlogComment  $blogComment
     * @return void
     */
    public function deleted(BlogComment $blogComment): void
    {
        //
    }

    /**
     * Handle the BlogComment "restored" event.
     *
     * @param  \App\Models\BlogComment  $blogComment
     * @return void
     */
    public function restored(BlogComment $blogComment): void
    {
        //
    }

    /**
     * Handle the BlogComment "force deleted" event.
     *
     * @param  \App\Models\BlogComment  $blogComment
     * @return void
     */
    public function forceDeleted(BlogComment $blogComment): void
    {
        //
    }
}
