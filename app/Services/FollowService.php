<?php

declare(strict_types=1);

namespace App\Services;

use App\Collections\UserCollection;
use App\Events\Follow\FollowedEvent;
use App\Models\Follower;
use App\Models\User;
use App\ProjectClass\ProjectCursorPaginator;
use App\Repositories\UserRepository;
use App\Services\ServiceHandlers\FollowServiceHandler;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * class FollowService
 * @package App\Services
 */
class FollowService
{
    /**
     * @var UserRepository $userRepository
     */
    public UserRepository $userRepository;

    private FollowServiceHandler $handler;

    public function __construct(UserRepository $userRepository, FollowServiceHandler $handler)
    {
        $this->userRepository = $userRepository;
        $this->handler = $handler;
    }

    /**
     * @param User $user
     * @return Paginator
     */
    public function getUsersToFollowPaginate(User $user): Paginator
    {
        return $this->userRepository->getUsersToFollowPaginate($user->user_id);
    }

    /**
     * @param User $forUser
     * @return Collection
     */
    public function popularUsersToFollow(User $forUser): Collection
    {
        return $this->userRepository->getPopularUsersToFollow($forUser->user_id);
    }

    /**
     * @param User $forUser
     * @param int $userId
     * @return string
     */
    public function followToggle(User $forUser, int $userId): string
    {
        /** @var User $foundFollowUser */
        $foundFollowUser = $this->userRepository->setSelect(
            array_merge(
                $this->userRepository->getSelect(),
                [
                    'follow_privacy',
                    'confirm_followers',
                    ]
            )
        )
            ->setWith(['followings'])
            ->find($userId);

        if ($forUser->user_id == $foundFollowUser->user_id) {
            throw new BadRequestException('Can\'t follow yourself');
        }

        $alreadyFollowing = $forUser->followings->contains('user_id', $userId);
        $requestSent = $forUser->followingRequests->contains('user_id', $userId);

        if ($alreadyFollowing || $requestSent) {
            if ($alreadyFollowing) {
                $forUser->followings()->detach($userId);
            } else {
                $forUser->followingRequests()->detach($userId);
            }

            return 'Unfollowed';
        } else if (
            $foundFollowUser->follow_privacy == User::FOLLOW_PRIVACY_EVERYONE ||
            (
                $foundFollowUser->follow_privacy == User::FOLLOW_PRIVACY_PEOPLE_I_FOLLOW &&
                $foundFollowUser->followings->contains('user_id', $forUser->user_id)
            )
        ) {
            if ($foundFollowUser->confirm_followers) {
                $forUser->followings()->attach($userId, ['active' => Follower::STATUS_NOT_ACTIVE]);

                return Follower::TEXT_FOLLOW_STATUSES[Follower::STATUS_NOT_ACTIVE];
            }

            $forUser->followings()->attach($userId, ['active' => Follower::STATUS_ACTIVE]);

            return Follower::TEXT_FOLLOW_STATUSES[Follower::STATUS_ACTIVE];
        }

        throw new BadRequestException('Due to the user\'s privacy settings, the user must follow you first.');
    }

    /**
     * @param int $currentUserId
     * @param int $perPage
     * @return ProjectCursorPaginator
     */
    public function getUserFollowers(int $currentUserId, int $perPage): ProjectCursorPaginator
    {
        /** @var ProjectCursorPaginator|UserCollection $followers */
        $followers = $this->userRepository->getFollowers($currentUserId, $perPage);
        $followers->setIsFriendAttribute($currentUserId);

        return $followers;
    }

    /**
     * @param int $currentUserId
     * @param int $userId
     * @param int $perPage
     * @return ProjectCursorPaginator
     */
    public function getFollowers(User $user, int $userId, int $perPage): ProjectCursorPaginator
    {
        $foundUser = $this->userRepository->getUser($userId);

        if ($this->handler->isShowFollowers($foundUser, $user->user_id)) {
            throw new BadRequestException('Forbidden by user\'s privacy settings.');
        }

        /** @var ProjectCursorPaginator|UserCollection $followers */
        $followers = $this->userRepository->getFollowers($userId, $perPage);
        $followers->setIsFriendAttribute($user->user_id);

        return $followers;
    }

    /**
     * @param int    $userId
     * @param int    $perPage
     * @param string $search
     * @return ProjectCursorPaginator
     */
    public function getFollowersSearch(int $userId, int $perPage, string $search): ProjectCursorPaginator
    {
        /** @var ProjectCursorPaginator|UserCollection $followers */
        $followers = $this->userRepository->getFollowersSearch($userId, $perPage, $search);
        $followers->setIsFriendAttribute($userId);
        $followers->each(function (User $user): void {
            $user->append('role');
            $user->makeHidden('admin');
        });

        return $followers;
    }

    /**
     * @param int $currentUserId
     * @param int $userId
     * @param int $perPage
     * @return ProjectCursorPaginator
     */
    public function getFollowings(int $currentUserId, int $userId, int $perPage): ProjectCursorPaginator
    {
        $user = $this->userRepository->getUser($userId);

        if (
            $user->friend_privacy == User::FRIEND_PRIVACY_NOBODY ||
            (
                $user->friend_privacy == User::FRIEND_PRIVACY_PEOPLE_I_FOLLOW &&
                !$user->followings->contains('user_id', $currentUserId)
            ) ||
            (
                $user->friend_privacy == User::FRIEND_PRIVACY_PEOPLE_FOLLOW_ME &&
                !$user->followers->contains('user_id', $currentUserId)
            )
        ) {
            throw new BadRequestException('Forbidden by user\'s privacy settings.');
        }

        /** @var UserCollection|ProjectCursorPaginator $followings */
        $followings = $this->userRepository->getFollowings($userId, $perPage);
        $followings->setIsFriendAttribute($currentUserId);

        return $followings;
    }

    /**
     * @param User $user
     * @param int $perPage
     * @return ProjectCursorPaginator
     */
    public function getUserFollowings(User $user, int $perPage): ProjectCursorPaginator
    {
        /** @var UserCollection|ProjectCursorPaginator $followings */
        $followings = $this->userRepository->getFollowings($user->user_id, $perPage);
        $followings->setIsFriendAttribute($user->user_id);

        return $followings;
    }

    /**
     * @param int    $userId
     * @param int    $perPage
     * @param string $search
     * @return ProjectCursorPaginator
     */
    public function getFollowingsSearch(int $userId, int $perPage, string $search): ProjectCursorPaginator
    {
        /** @var UserCollection|ProjectCursorPaginator $followings */
        $followings = $this->userRepository->getFollowingsSearch($userId, $perPage, $search);

        $followings->setIsFriendAttribute($userId);
        $followings->each(function (User $user) use ($userId): void {
            $user->setAttribute('role', $user->role);
            $user->makeHidden('admin');
        });

        return $followings;
    }

    /**
     * @param int $currentUserId
     * @param int $userId
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function getFollowingRequests(int $userId, int $perPage = null): ProjectCursorPaginator
    {
        /** @var User $foundUser */
        $foundUser = $this->userRepository->find($userId, array_merge($this->userRepository->getSelect(), ['confirm_followers']));

        return $this->userRepository->getFollowingRequests($foundUser, $perPage);
    }

    /**
     * @param User $forUser
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function getFollowRecommendations(User $forUser, int $perPage = null): ProjectCursorPaginator
    {
        /** @var ProjectCursorPaginator|UserCollection $recommendations */
        $recommendations = $this->userRepository->getFollowRecommendations($forUser->user_id, $perPage);
        $recommendations->setIsFriendAttribute($forUser->user_id);

        return $recommendations;
    }

    /**
     * @param User $forUser
     * @param int $userId
     * @return string
     */
    public function acceptFollowingRequest(User $forUser, int $userId): bool
    {
        if ($forUser->followerRequests->contains('user_id', $userId)) {
            $forUser->followerRequests()->updateExistingPivot($userId, ['active' => Follower::STATUS_ACTIVE]);

            return true;
        }

        return false;
    }

    /**
     * @param User $forUser
     * @param int $userId
     * @return bool
     */
    public function declineFollowingRequest(User $forUser, int $userId): bool
    {
        if ($forUser->followerRequests->contains('user_id', $userId)) {
            $forUser->followerRequests()->detach($userId);

            return true;
        }

        return false;
    }
}
