<?php

declare(strict_types=1);

namespace App\Observers;

use App\Events\Follow\FollowedEvent;
use App\Models\Follower;

/**
 * class FollowerObserver
 * @package App\Observers
 */
class FollowerObserver
{

    public function creating(Follower $follower): void
    {
        //
    }

    /**
     * Handle the Follower "created" event.
     *
     * @param  \App\Models\Follower  $follower
     * @return void
     */
    public function created(Follower $follower): void
    {
        FollowedEvent::dispatch($follower, $follower->pivotParent->username);
    }

    /**
     * Handle the Follower "updated" event.
     *
     * @param  \App\Models\Follower  $follower
     * @return void
     */
    public function updated(Follower $follower): void
    {
        //
    }

    /**
     * Handle the Follower "deleted" event.
     *
     * @param  \App\Models\Follower  $follower
     * @return void
     */
    public function deleted(Follower $follower): void
    {
        //
    }

    /**
     * Handle the Follower "restored" event.
     *
     * @param  \App\Models\Follower  $follower
     * @return void
     */
    public function restored(Follower $follower): void
    {
        //
    }

    /**
     * Handle the Follower "force deleted" event.
     *
     * @param  \App\Models\Follower  $follower
     * @return void
     */
    public function forceDeleted(Follower $follower): void
    {
        //
    }
}
