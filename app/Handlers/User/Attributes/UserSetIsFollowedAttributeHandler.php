<?php

namespace App\Handlers\User\Attributes;

use App\Models\User;

/**
 * class SetIsFollowedAttribute
 * @package App\Handlers\User\Attributes
 */
class UserSetIsFollowedAttributeHandler
{
    /**
     * @param User $user
     * @param int $userId
     * @return User
     */
    public static function execute(User $user, int $userId): User
    {
        if (!empty($user->user_id)) {
            if ($user->followers->contains('user_id', $userId)) {
                $isFriend = 'following';
            } else if ($user->followerRequests->contains('user_id', $userId)) {
                $isFriend = 'requested';
            } else {
                $isFriend = 'follow';
            }

            $user->setAttribute('is_friend', $isFriend);
        }

        return $user;
    }
}
