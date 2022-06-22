<?php

declare(strict_types=1);

namespace App\Policies\Gates;

use App\Models\Group;
use App\Models\User;
use App\Repositories\GroupMemberRepository;

/**
 * class GroupMember
 * @package App\Policies\Gates
 */
class GroupMember
{
    /**
     * @param User $user
     * @param int $groupId
     * @return bool
     */
    public function memberInviteFriends(User $user, int $groupId): bool
    {
        /** @var GroupMemberRepository $repository */
        $repository = resolve(GroupMemberRepository::class);

        return $repository->isMember($groupId, $user->user_id, true);
    }
}
