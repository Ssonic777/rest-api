<?php

declare(strict_types=1);

namespace App\Policies\Gates;

use App\Models\Post;
use App\Models\User;
use App\Repositories\GroupMemberRepository;

/**
 * class GroupPostGate
 * @package App\Policies\Gates
 */
class GroupPostGate
{

    /**
     * @param User $user
     * @param int $groupId
     * @return bool
     */
    public function memberCreatePost(User $user, int $groupId): bool
    {
        /** @var GroupMemberRepository $repository */
        $repository = resolve(GroupMemberRepository::class);

        return $repository->isMember($groupId, $user->user_id, true);
    }

    /**
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function memberUpdatePost(User $user, Post $post): bool
    {
        return $user->user_id == $post->user_id;
    }

    /**
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function memberDeletePost(User $user, Post $post): bool
    {
        return $user->user_id == $post->user_id;
    }
}
