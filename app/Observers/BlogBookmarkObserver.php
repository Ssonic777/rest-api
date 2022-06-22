<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\BlogBookmark;

/**
 * class BlogBookmarkObserver
 * @package App\Observer
 */
class BlogBookmarkObserver
{
    /**
     * @param BlogBookmark $blogBookmark
     */
    public function creating(BlogBookmark $blogBookmark): void
    {
        $blogBookmark->setAttribute('time', time());
    }

    /**
     * Handle the BlogBookmark "created" event.
     *
     * @param  \App\Models\BlogBookmark  $blogBookmark
     * @return void
     */
    public function created(BlogBookmark $blogBookmark): void
    {
        //
    }

    /**
     * Handle the BlogBookmark "updated" event.
     *
     * @param  \App\Models\BlogBookmark  $blogBookmark
     * @return void
     */
    public function updated(BlogBookmark $blogBookmark): void
    {
        //
    }

    /**
     * Handle the BlogBookmark "deleted" event.
     *
     * @param  \App\Models\BlogBookmark  $blogBookmark
     * @return void
     */
    public function deleted(BlogBookmark $blogBookmark): void
    {
        //
    }

    /**
     * Handle the BlogBookmark "restored" event.
     *
     * @param  \App\Models\BlogBookmark  $blogBookmark
     * @return void
     */
    public function restored(BlogBookmark $blogBookmark): void
    {
        //
    }

    /**
     * Handle the BlogBookmark "force deleted" event.
     *
     * @param  \App\Models\BlogBookmark  $blogBookmark
     * @return void
     */
    public function forceDeleted(BlogBookmark $blogBookmark): void
    {
        //
    }
}
