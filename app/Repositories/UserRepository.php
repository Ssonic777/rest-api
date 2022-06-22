<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Follower;
use App\Models\User;
use App\ProjectClass\ProjectCursorPaginator;
use App\Repositories\Base\BaseModelRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\Paginator;

/**
 * class UserRepository
 * @package App\Repositories
 */
class UserRepository extends BaseModelRepository
{

    /** @var string[] DEFAULT_SELECT */
    public const DEFAULT_SELECT = ['user_id', 'username', 'first_name', 'last_name', 'avatar', 'email'];

    protected function initializeDefaultData(): void
    {
        $this->setSelect(self::DEFAULT_SELECT);
    }

    protected function getModel(): string
    {
        return User::class;
    }

    public function getUsersPaginate(int $perPage = 25): CursorPaginator
    {
        return $this->getModelClone()->newQuery()
                                    ->select($this->getSelect())
                                    ->cursorPaginateExtended($perPage, 'user_id');
    }

    /**
     * @param string $email
     * @return bool
     */
    public function checkEmail(string $email): bool
    {
        return $this->getModelClone()->newQuery()->where('email', $email)->doesntExist();
    }

    /**
     * @param int $userId
     * @return mixed
     */
    public function getPopularUsersToFollow(int $forUserId): Collection
    {
        return $this->getModelClone()->newQuery()
            ->select($this->getSelect())
            ->withCount(['followers' => function ($query) {
                $query->where('Wo_Followers.active', '1');
            }])
            ->where('active', '1')
            ->where('user_id', '!=', $forUserId)
            ->whereNotIn('user_id', function ($query) use ($forUserId) {
                $query->select('following_id')
                    ->from('Wo_Followers')
                    ->where('follower_id', $forUserId);
            })
            ->orderBy('followers_count', 'desc')
            ->limit(25)
            ->get();
    }

    /**
     * @param int $userId
     * @return Paginator
     */
    public function getUsersToFollowPaginate(int $userId): Paginator
    {
        return $this->getModelClone()->newQuery()->select(self::DEFAULT_SELECT)
                                                ->where('user_id', '!=', $userId)
                                                ->whereNotIn('user_id', function ($query) use ($userId): void {
                                                    $query->select('following_id')
                                                        ->from('Wo_Followers')
                                                        ->where('follower_id', '=', $userId);
                                                })
                                                ->where('active', '=', '1')
                                                ->orderBy('user_id', 'desc')
                                                ->cursorPaginateExtended();
    }

    /**
     * @param User $foundUser
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function getFollowingRequests(User $foundUser, int $perPage = null): ProjectCursorPaginator
    {
        return $foundUser->followerRequests()
            ->select([
                'user_id',
                'username',
                'email',
                'first_name',
                'last_name',
                'avatar',
                'created_at',
            ])
            ->projectCursorPaginate($perPage);
    }

    /**
     * @param int $forUserId
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function getFollowRecommendations(int $forUserId, int $perPage = null): ProjectCursorPaginator
    {
        return $this->getModelClone()->newQuery()
            ->select(['user_id', 'first_name', 'last_name', 'avatar'])
            ->with(['field'])
            ->where('active', '1')
            ->where('user_id', '!=', $forUserId)
            ->whereIn('user_id', function ($query) use ($forUserId) {
                $query->select('user_id')
                    ->from('Wo_Group_Members')
                    ->whereIn('group_id', function ($query) use ($forUserId) {
                        $query->select('group_id')
                            ->from('Wo_Group_Members')
                            ->where('user_id', $forUserId);
                    });
            })
            ->whereNotIn('user_id', function ($query) use ($forUserId) {
                $query->select('following_id')
                    ->from('Wo_Followers')
                    ->where('follower_id', $forUserId);
            })
            ->whereNotIn('user_id', function ($query) use ($forUserId) {
                $query->select('blocked')
                    ->from('Wo_Blocks')
                    ->where('blocker', $forUserId);
            })
            ->projectCursorPaginate($perPage);
    }

    /**
     * @param int $userId
     * @return CursorPaginator
     */
    public function getUserChats(int $userId): CursorPaginator
    {
        return $this->getModelClone()->newQuery()
                                    ->with([
                                        'admins',
                                        'users',
                                    ])
                                    ->where('user_id', '=', $userId)
                                    ->orWhereHas('admins', function (Builder $query) use ($userId) {
                                        return $query->where('UserID', '=', $userId);
                                    })
                                    ->orWhereHas('users', function (Builder $query) use ($userId) {
                                        return $query->where('user_id', '=', $userId);
                                    })
                                    ->cursorPaginateExtended();
    }

    /**
     * @param User $user
     * @param int $postId
     * @return bool
     */
    public function postLikeDoesntExists(User $user, int $postId): bool
    {
        return $user->postLikes()
                    ->newExistingPivot(['post_id' => $postId])
                    ->doesntExist();
    }

    /**
     * @param int $userId
     * @param string $search
     * @param string|null $searchBy
     * @return CursorPaginator
     */
    public function searchFollows(int $userId, string $search, string $searchBy = null): CursorPaginator
    {
        /** @var User $foundUser */
        $foundUser = $this->find($userId);

        return $foundUser->$searchBy()
                        ->select(['user_id', 'username', 'first_name', 'last_name', 'avatar'])
                        ->where([
                            ['username', 'LIKE', "%{$search}%", 'or'],
                            ['first_name', 'LIKE', "%{$search}%", 'or'],
                            ['last_name', 'LIKE', "%{$search}%", 'or']
                        ])
                        ->cursorPaginate();
    }

    /**
     * @param int $userId
     * @param string|null $active
     * @param int|null $perPage
     * @return CursorPaginator
     */
    public function getUserGroups(int $userId, ?string $active = null, int $perPage = null): CursorPaginator
    {
        return is_null($active) ? $this->getUserAllGroups($userId, $perPage)
                                : $this->getUserGroupsByActive($userId, $active, $perPage);
    }

    /**
     * @param int $userId
     * @param int|null $perPage
     * @return CursorPaginator
     */
    public function getUserAllGroups(int $userId, int $perPage = null): CursorPaginator
    {
        return $this->getModelClone()->newQuery()
                                    ->findOrFail($userId)
                                    ->groups()
                                    ->with('catry')
                                    ->withCount('members')
                                    ->cursorPaginateExtended($perPage, 'group_id');
    }

    /**
     * @param int $userId
     * @param string $active
     * @param int|null $perPage
     * @return CursorPaginator
     */
    public function getUserGroupsByActive(int $userId, string $active, int $perPage = null): CursorPaginator
    {
        /** @var User $foundUser */
        return $this->getModelClone()->newQuery()
                                    ->findOrFail($userId)
                                    ->groups()
                                    ->wherePivot('active', '=', $active)
                                    ->with('catry')
                                    ->withCount('members')
                                    ->cursorPaginateExtended($perPage, 'group_id');
    }

    /**
     * @param int $userId
     * @param int $perPage
     * @return ProjectCursorPaginator
     */
    public function getFollowers(int $userId, int $perPage): ProjectCursorPaginator
    {
        return $this->getModelClone()->newQuery()
                                    ->findOrFail($userId)
                                    ->followers()
                                    ->select([
                                        'user_id',
                                        'first_name',
                                        'last_name',
                                        'avatar',
                                    ])
                                    ->projectCursorPaginate($perPage);
    }

    /**
     * @param int $userId
     * @param int $perPage
     * @param string $search
     * @return ProjectCursorPaginator
     */
    public function getFollowersSearch(int $userId, int $perPage, string $search): ProjectCursorPaginator
    {
        return $this->getModelClone()->newQuery()
            ->findOrFail($userId)
            ->followers()
            ->select([
                'user_id',
                'first_name',
                'last_name',
                'username',
                'avatar',
                'admin'
            ])->where([
                ['username', 'LIKE', "%{$search}%", 'or'],
                ['first_name', 'LIKE', "%{$search}%", 'or'],
                ['last_name', 'LIKE', "%{$search}%", 'or']
            ])
            ->projectCursorPaginate($perPage);
    }

    /**
     * @param int $userId
     * @param int $perPage
     * @return ProjectCursorPaginator
     */
    public function getFollowings(int $userId, int $perPage): ProjectCursorPaginator
    {
        return $this->getModelClone()->newQuery()
                                    ->findOrFail($userId)
                                    ->followings()
                                    ->select([
                                        'user_id',
                                        'first_name',
                                        'last_name',
                                        'avatar',
                                    ])
                                    ->projectCursorPaginate($perPage);
    }

    /**
     * @param int $userId
     * @param int $perPage
     * @param string $search
     * @return ProjectCursorPaginator
     */
    public function getFollowingsSearch(int $userId, int $perPage, string $search): ProjectCursorPaginator
    {
        return $this->getModelClone()->newQuery()
            ->findOrFail($userId)
            ->followings()
            ->select([
                'user_id',
                'first_name',
                'last_name',
                'username',
                'avatar',
                'admin',
            ])->where([
                ['username', 'LIKE', "%{$search}%", 'or'],
                ['first_name', 'LIKE', "%{$search}%", 'or'],
                ['last_name', 'LIKE', "%{$search}%", 'or']
            ])
            ->projectCursorPaginate($perPage);
    }

    /**
     * @param string $search
     * @param array|string[] $columns
     * @return Collection
     */
    public function search(string $search, array $columns = ['first_name']): Collection
    {
        $build = $this->getModelClone()->newQuery()
                                    ->select($this->getSelect());

        foreach ($columns as $column) {
            $build->where($column, 'LIKE', "%{$search}%", 'or');
        }

        return $build->get();
    }

    /**
     * @param int $userId
     * @return User
     */
    public function getUser(int $userId): User
    {
        return $this->getModelClone()
            ->newQuery()
            ->select(array_merge($this->getSelect(), [
                'post_privacy',
                'friend_privacy',
            ]))
            ->find($userId);
    }

    public function firstOrCreate(array $attributes, array $values): User
    {
        return $this->getModelClone()->newQuery()
                                    ->firstOrCreate($attributes, $values);
    }

    /**
     * @param int $userId
     * @return User
     */
    public function getUserNotificationsSettings(int $userId): User
    {
        return $this->getModelClone()
            ->newQuery()
            ->select(array_merge($this->getSelect(), [
                'notifications_allow',
                'notifications_sound',
                'notifications_from',
            ]))
            ->with('followers')
            ->where('user_id', $userId)
            ->first();
    }
}
