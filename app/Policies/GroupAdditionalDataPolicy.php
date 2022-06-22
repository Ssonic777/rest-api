<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\GroupAdditionalData;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * class GroupAdditionalDataPolicy
 * @package App\Policies
 */
class GroupAdditionalDataPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  User  $user
     * @return mixed
     */
    public function viewAny(User $user): void
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  GroupAdditionalData  $groupAdditionalData
     * @return mixed
     */
    public function view(User $user, GroupAdditionalData $groupAdditionalData): bool
    {
        return $user->user_id == $groupAdditionalData->group()->value('user_id');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return mixed
     */
    public function create(User $user): void
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  GroupAdditionalData  $groupAdditionalData
     * @return bool
     */
    public function update(User $user, GroupAdditionalData $groupAdditionalData): bool
    {
        return $user->user_id == $groupAdditionalData->group()->value('user_id');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  GroupAdditionalData  $groupAdditionalData
     * @return mixed
     */
    public function delete(User $user, GroupAdditionalData $groupAdditionalData): void
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  GroupAdditionalData  $groupAdditionalData
     * @return mixed
     */
    public function restore(User $user, GroupAdditionalData $groupAdditionalData): void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  GroupAdditionalData  $groupAdditionalData
     * @return mixed
     */
    public function forceDelete(User $user, GroupAdditionalData $groupAdditionalData): void
    {
        //
    }
}
