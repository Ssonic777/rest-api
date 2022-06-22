<?php

namespace App\Policies;

use App\Models\GroupChat;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupChatPolicy
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
     * @param  \App\Models\GroupChat  $groupChat
     * @return mixed
     */
    public function view(User $user, GroupChat $groupChat): bool
    {
        return $groupChat->users()->get()->contains('user_id', $user->user_id) || $groupChat->user_id === $user->user_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user): void
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GroupChat  $groupChat
     * @return mixed
     */
    public function update(User $user, GroupChat $groupChat): bool
    {
        return $groupChat->user_id === $user->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GroupChat  $groupChat
     * @return mixed
     */
    public function delete(User $user, GroupChat $groupChat): bool
    {
        return $groupChat->user_id === $user->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GroupChat  $groupChat
     * @return mixed
     */
    public function restore(User $user, GroupChat $groupChat): void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GroupChat  $groupChat
     * @return mixed
     */
    public function forceDelete(User $user, GroupChat $groupChat): void
    {
        //
    }
}
