<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Group;
use App\Models\GroupMember;

/**
 * class GroupObserver
 * @package App\Observers
 */
class GroupObserver
{
    /**
     * @param Group $group
     */
    public function creating(Group $group): void
    {
        $group->setAttribute('active', Group::GROUP_ACTIVE);
    }

    /**
     * Handle the Group "created" event.
     *
     * @param  \App\Models\Group  $group
     * @return void
     */
    public function created(Group $group): void
    {
        $group->setting()->create();

        $data = [
            'time' => time(),
            'active' => GroupMember::ACTIVE
        ];

        $group->members()->attach($group->user_id, $data);
    }

    /**
     * Handle the Group "updated" event.
     *
     * @param  \App\Models\Group  $group
     * @return void
     */
    public function updated(Group $group): void
    {
        //
    }

    /**
     * Handle the Group "deleted" event.
     *
     * @param  \App\Models\Group  $group
     * @return void
     */
    public function deleted(Group $group): void
    {
        $group->setAttribute('active', Group::GROUP_NOT_ACTIVE);
        $group->save();
    }

    /**
     * Handle the Group "restored" event.
     *
     * @param  \App\Models\Group  $group
     * @return void
     */
    public function restored(Group $group): void
    {
        //
    }

    /**
     * Handle the Group "force deleted" event.
     *
     * @param  \App\Models\Group  $group
     * @return void
     */
    public function forceDeleted(Group $group): void
    {
        //
    }
}
