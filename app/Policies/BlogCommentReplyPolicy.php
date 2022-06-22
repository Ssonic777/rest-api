<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BlogCommentReply;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * class BlogCommentReplyPolicy
 * @package App\Policies
 */
class BlogCommentReplyPolicy
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
     * @param  \App\Models\BlogCommentReply  $blogCommentReply
     * @return mixed
     */
    public function view(User $user, BlogCommentReply $blogCommentReply): void
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
     * @param  \App\Models\BlogCommentReply  $blogCommentReply
     * @return mixed
     */
    public function update(User $user, BlogCommentReply $blogCommentReply): bool
    {
        return $blogCommentReply->user_id == $user->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BlogCommentReply  $blogCommentReply
     * @return mixed
     */
    public function delete(User $user, BlogCommentReply $blogCommentReply): bool
    {
        return $blogCommentReply->user_id == $user->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BlogCommentReply  $blogCommentReply
     * @return mixed
     */
    public function restore(User $user, BlogCommentReply $blogCommentReply): void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BlogCommentReply  $blogCommentReply
     * @return mixed
     */
    public function forceDelete(User $user, BlogCommentReply $blogCommentReply): void
    {
        //
    }
}
