<?php

declare(strict_types=1);

namespace App\Services\ServiceHandlers;

use App\Models\User;

/**
 * class FollowServiceHandler
 * @package App\Services\ServiceHandlers
 */
class FollowServiceHandler
{
    /**
     * @param User $user
     * @param int $currentUserId
     * @return bool
     */
    public function isShowFollowers(User $user, int $currentUserId): bool
    {
        $result = true;

        if ($user->friend_privacy == User::FRIEND_PRIVACY_EVERYONE) {
            $result = !$result;
        } else if ($user->friend_privacy == User::FRIEND_PRIVACY_PEOPLE_FOLLOW_ME) {
            $result = !$user->followers->contains('user_id', $currentUserId);
        } else if ($user->friend_privacy == User::FRIEND_PRIVACY_PEOPLE_I_FOLLOW) {
            $result = !$user->followings->contains('user_id', $currentUserId);
        }

        return ($user->friend_privacy == User::FRIEND_PRIVACY_NOBODY) ?: $result;
    }
}
