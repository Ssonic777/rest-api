<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Collections\GroupCollection;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use App\Repositories\Base\BaseModelRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\CursorPaginator;

/**
 * class GroupRepository
 * @package App\Repositories
 */
class GroupRepository extends BaseModelRepository
{

    public const DEFAULT_SELECT = ['id', 'user_id', 'group_name', 'group_title', 'avatar', 'cover', 'privacy', 'about', 'category'];
    public const DEFAULT_WITH = ['catry.lang'];
    public const DEFAULT_WITH_COUNT = ['members'];

    protected function initializeDefaultData(): void
    {
        $this->setSelect(self::DEFAULT_SELECT);
        $this->setWith(self::DEFAULT_WITH);
        $this->setWithCount(self::DEFAULT_WITH_COUNT);
    }

    public function getModel(): string
    {
        return Group::class;
    }

    /**
     * @param int $userId
     * @param int $perPage
     * @return CursorPaginator
     */
    public function getUserCreatedGroups(int $userId, int $perPage = 15): CursorPaginator
    {
        return $this->getModelClone()->newQuery()
                                    ->select($this->getSelect())
                                    ->where('user_id', '=', $userId)
                                    ->with($this->getWith())
                                    ->withCount($this->getWithCount())
                                    ->orderBy('id', 'DESC')
                                    ->cursorPaginateExtended($perPage);
    }

    /**
     * @param int|null $perPage
     * @return CursorPaginator
     */
    public function getGroups(?int $perPage = null): CursorPaginator
    {
        return $this->getModelClone()->newQuery()
                                    ->with($this->getWith())
                                    ->withCount($this->getWithCount())
                                    ->cursorPaginateExtended($perPage);
    }

    /**
     * @param int $userId
     * @param array $with
     * @param int|null $perPage
     * @return CursorPaginator
     */
    public function getUserGroups(int $userId, array $with = [], ?int $perPage = null): CursorPaginator
    {
        return $this->getModelClone()->newQuery()
                                    ->where('user_id', '=', $userId)
                                    ->with($with)
                                    ->cursorPaginateExtended($perPage);
    }

    /**
     * @param int $id
     * @return Model
     */
    public function showGroup(int $id): Model
    {
        return $this->getModelClone()->newQuery()
                                    ->select($this->getSelect())
                                    ->with($this->getWith())
                                    ->withCount($this->getWithCount())
                                    ->findOrFail($id);
    }

    /**
     * @param int $userId
     * @param int $groupId
     * @return bool
     */
    public function isAuthorThisGroup(int $userId, int $groupId): bool
    {
        return $this->getModelClone()->newQuery()
                                    ->where([
                                        ['id', '=', $groupId],
                                        ['user_id', '=', $userId]
                                    ])
                                    ->exists();
    }

    /**
     * @param int $userId
     * @param int|null $perPage
     * @return CursorPaginator
     */
    public function getSuggests(int $userId, int $perPage = 0): CursorPaginator
    {
        /** @var User $user */
        $user = app()->call(UserRepository::class . '@find', ['id' => $userId]);
        $userGroupIds = $user->groups()->wherePivot('active', '=', GroupMember::ACTIVE)
                                        ->with('catry')
                                        ->pluck('Wo_Groups.id');

        $userCreatedGroupIds = $this->getModelClone()->newQuery()->where('user_id', '=', $userId)->pluck('id');
        $ids = array_unique(array_merge($userGroupIds->toArray(), $userCreatedGroupIds->toArray()));

        return $this->getModelClone()->newQuery()
                                    ->select($this->getSelect())
                                    ->whereNotIn('id', $ids)
                                    ->withCount($this->getWithCount())
                                    ->when(
                                        in_array('members', $this->getWithCount()),
                                        fn(Builder $query): Builder => $query->orderBy('members_count', 'DESC')
                                    )
                                    ->orderBy('id', 'DESC')
                                    ->with($this->getWith())
                                    ->cursorPaginateExtended($perPage);
    }

    /**
     * @param int $groupId
     * @param string $active
     * @param int $perPage
     * @return CursorPaginator
     */
    public function getMembers(int $groupId, string $active, int $perPage = 15): CursorPaginator
    {
        return $this->getModelClone()->newQuery()
                                    ->findOrFail($groupId)
                                    ->members()
                                    ->select(['Wo_Users.user_id', 'username', 'first_name', 'last_name', 'avatar', 'email'])
                                    ->wherePivot('active', '=', $active)
                                    ->cursorPaginateExtended($perPage);
    }

    /**
     * @param int $groupId
     * @param string $active
     * @return int
     */
    public function getMembersCont(int $groupId, string $active): int
    {
        return $this->getModelClone()->newQuery()
                                    ->findOrFail($groupId)
                                    ->members()
                                    ->wherePivot('active', '=', $active)
                                    ->count();
    }

    /**
     * @param int $groupId
     * @param int $userId
     * @return bool
     */
    public function requestAccept(int $groupId, int $userId): bool
    {
        $this->getModelClone()->newQuery()
                            ->findOrFail($groupId)
                            ->members()
                            ->updateExistingPivot($userId, ['active' => GroupMember::ACTIVE]);



        return true;
    }

    /**
     * @param int $groupId
     * @param int $userId
     * @return bool
     */
    public function requestDecline(int $groupId, int $userId): bool
    {
        $this->getModelClone()->newQuery()
                            ->findOrFail($groupId)
                            ->members()
                            ->detach($userId);

        return true;
    }
}
