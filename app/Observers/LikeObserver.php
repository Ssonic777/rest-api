<?php

declare(strict_types=1);

namespace App\Observers;

use App\Facades\Notify;
use App\Models\Like;
use App\Notifications\Post\PostLikeNotification;

/**
 * class LikeObserver
 * @package App\Observer
 */
class LikeObserver
{
    /**
     * Handle the Like "created" event.
     *
     * @param  \App\Models\Like  $like
     * @return void
     */
    public function created(Like $like): void
    {
        if ($like->post->enable_notifications) {
            $data = [
                'post_id' => $like->post_id,
            ];

            Notify::store($like->user_id, $like->post->user_id, PostLikeNotification::TYPE, $data);
        }
    }

    /**
     * Handle the Like "updated" event.
     *
     * @param  \App\Models\Like  $like
     * @return void
     */
    public function updated(Like $like): void
    {
        //
    }

    /**
     * Handle the Like "deleted" event.
     *
     * @param  \App\Models\Like  $like
     * @return void
     */
    public function deleted(Like $like): void
    {
        //
    }

    /**
     * Handle the Like "restored" event.
     *
     * @param  \App\Models\Like  $like
     * @return void
     */
    public function restored(Like $like): void
    {
        //
    }

    /**
     * Handle the Like "force deleted" event.
     *
     * @param  \App\Models\Like  $like
     * @return void
     */
    public function forceDeleted(Like $like): void
    {
        //
    }
}
