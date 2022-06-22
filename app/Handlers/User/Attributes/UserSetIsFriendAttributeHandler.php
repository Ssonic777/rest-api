<?php

declare(strict_types=1);

namespace App\Handlers\User\Attributes;

use App\Models\Follower;
use App\Models\User;

/**
 * class UserSetIsFriendAttributeHandler
 * @package App\Handlers\User\Attributes
 */
class UserSetIsFriendAttributeHandler
{
    /**
     * @param User $user
     * @param int $currentUserId
     * @return User
     */
    public static function execute(User $user, int $currentUserId): User
    {
        if ($user->followers()->updateExistingPivot($currentUserId, ['active' => Follower::STATUS_ACTIVE])) {
            $isFriend = 'following';
        } else if ($user->followerRequests()->updateExistingPivot($currentUserId, ['active' => Follower::STATUS_NOT_ACTIVE])) {
            $isFriend = 'requested';
        } else {
            $isFriend = 'follow';
        }

        $user->setAttribute('is_friend', $isFriend);

        return $user;
    }
}
