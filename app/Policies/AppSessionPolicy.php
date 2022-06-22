<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AppSession;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * class AppSessionPolicy
 * @package App\Policies
 */
class AppSessionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AppSession  $appSession
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, AppSession $appSession): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AppSession  $appSession
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, AppSession $appSession): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AppSession  $appSession
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, AppSession $appSession): bool
    {
        return $user->user_id == $appSession->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AppSession  $appSession
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, AppSession $appSession): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AppSession  $appSession
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, AppSession $appSession): bool
    {
        //
    }
}
