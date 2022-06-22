<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BlogComment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * class BlogCommentPolicy
 * @package App\Polices
 */
class BlogCommentPolicy
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
     * @param  \App\Models\BlogComment  $blogComment
     * @return mixed
     */
    public function view(User $user, BlogComment $blogComment): void
    {
        //
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
     * @param  \App\Models\BlogComment  $blogComment
     * @return mixed
     */
    public function update(User $user, BlogComment $blogComment): bool
    {
        return $user->user_id == $blogComment->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BlogComment  $blogComment
     * @return mixed
     */
    public function delete(User $user, BlogComment $blogComment): bool
    {
        return $user->user_id == $blogComment->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BlogComment  $blogComment
     * @return mixed
     */
    public function restore(User $user, BlogComment $blogComment): void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BlogComment  $blogComment
     * @return mixed
     */
    public function forceDelete(User $user, BlogComment $blogComment): void
    {
        //
    }
}
