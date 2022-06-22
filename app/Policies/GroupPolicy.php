<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * class GroupPolicy
 * @package App\Policies
 */
class GroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user): void
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return mixed
     */
    public function view(User $user, Group $group): void
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
         return (bool)$user->active;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Group  $group
     * @return bool
     */
    public function update(User $user, Group $group): bool
    {
        return $user->user_id === $group->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return mixed
     */
    public function delete(User $user, Group $group): bool
    {
        return $user->user_id === $group->user_id;
    }

    /**
     * @param User $user
     * @param Group $group
     */
    public function restore(User $user, Group $group): void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Group  $group
     * @return mixed
     */
    public function forceDelete(User $user, Group $group): void
    {
        //
    }
}
