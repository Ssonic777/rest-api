<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\Contracts\ExceptionMessageInterface;
use App\Models\Contracts\NotificationInterface;
use App\Http\Filters\GroupFilter;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\Datas\Group\GroupInviteData;
use App\Policies\Gates\Contracts\GatePrefixInterface;
use App\Repositories\GroupAdminRepository;
use App\Repositories\GroupMemberRepository;
use App\Repositories\GroupRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Notify;

/**
 * class GroupActionService
 * @package App\Services
 */
class GroupActionService
{

    /**
     * @var array|string[] $groupTabs
     */
    public const GROUP_TABS = [
        'my',
        'suggested',
        'joined'
    ];

    /**
     * @var GroupRepository $groupRepository
     */
    private GroupRepository $groupRepository;

    /**
     * @var GroupMemberRepository $groupMemberRepository
     */
    private GroupMemberRepository $groupMemberRepository;

    /**
     * @var UserRepository $userRepository
     */
    private UserRepository $userRepository;

    public function __construct(GroupRepository $groupRepository, GroupMemberRepository $groupMemberRepository, UserRepository $userRepository)
    {
        $this->groupRepository = $groupRepository;
        $this->groupMemberRepository = $groupMemberRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param User $user
     * @param string|null $tab
     * @param int|null $perPage
     * @return CursorPaginator
     */
    public function getTabGroups(User $user, ?string $tab = null, int $perPage = null): CursorPaginator
    {
        switch ($tab) {
            case 'my':
                return $this->userCreatedGroups($user->user_id, $perPage);
            case 'suggested':
                return $this->suggestions($user, $perPage);
            case 'joined':
                return $this->userJoinedGroups($user->user_id, 'joined', $perPage);
            default:
                return $this->getGroups($user, $perPage);
        }
    }

    /**
     * @param int $userId
     * @param int $perPage
     * @return CursorPaginator
     */
    public function userCreatedGroups(int $userId, int $perPage = 15): CursorPaginator
    {
        $this->groupRepository->setSelect(array_merge($this->groupRepository->getSelect(), ['active', 'join_privacy']));
        $userCreatedGroups = $this->groupRepository->getUserCreatedGroups($userId, $perPage);
        $userCreatedGroups->each(fn(Group $group) => $this->setUserAttributes($group, $userId));

        return $userCreatedGroups;
    }

    /**
     * @param User $user
     * @param int|null $perPage
     * @return CursorPaginator
     */
    public function suggestions(User $user, int $perPage = null): CursorPaginator
    {
        $suggestGroups = $this->groupRepository->getSuggests($user->user_id, $perPage);
        $suggestGroups->each(fn (Group $group): Group => $this->setUserAttributes($group, $user->user_id));

        return $suggestGroups;
    }

    /**
     * @param int $userId
     * @param string|null $joinedType
     * @param int|null $perPage
     * @return CursorPaginator
     */
    public function userJoinedGroups(int $userId, string $joinedType = null, int $perPage = null): CursorPaginator
    {
        $joinedType = $joinedType == 'joined' ? GroupMember::ACTIVE : GroupMember::NOT_ACTIVE;

        /** @var CursorPaginator $suggestGroups */
        $suggestGroups = $this->userRepository->getUserGroups($userId, $joinedType, $perPage);

        $suggestGroups->each(function (Group $group) use ($userId): void {
            $this->setUserAttributes($group, $userId);
            $this->offsetUnsetAttributes($group, ['about', 'join_privacy', 'active', 'registered', 'deleted_at']);
        });

        return $suggestGroups;
    }

    /**
     * @param Group $group
     * @param int $userId
     * @return Group
     */
    public function setUserAttributes(Group $group, int $userId): Group
    {
        $isOwner = $group->user_id == $userId;
        $isAdmin = app()->call(GroupAdminRepository::class . '@isAdminThisGroup', ['userId' => $userId, 'groupId' => $group->id]);
        $isJoined = $this->groupMemberRepository->isMember($group->id, $userId, true);
        $isRequested = $this->groupMemberRepository->isMember($group->id, $userId, false);

        $group->setAttribute('is_owner', $isOwner);
        $group->setAttribute('is_admin', $isAdmin);
        $group->setAttribute('is_joined', $isJoined);
        $group->setAttribute('is_requested', $isRequested);

        return $group;
    }

    /**
     * @param int $groupId
     * @param string|null $joinedType
     * @param int|null $perPage
     * @return array
     */
    public function groupMembers(int $groupId, string $joinedType = null, int $perPage = null): array
    {
        if (!is_null($joinedType)) {
            $joinedType = $joinedType == 'joined';
        }

        $groupWithMembers = $this->groupMemberRepository->getGroupMembers($groupId, $joinedType, $perPage);
        $groupMembersCount = $this->groupMemberRepository->getGroupMembersCont($groupId, $joinedType);

        /** @var GroupAdminRepository $repo */
        $repo = resolve(GroupAdminRepository::class);
        /** @var Collection $groupMembers */
        $groupMembers = $groupWithMembers->map(function (GroupMember $groupMember) use ($repo, $groupId): User {
            /** @var User $member */
            $member = $groupMember->member;

            $setAttributes = [
                'is_owner' => $member->myGroups->contains('id', $groupId),
                'is_admin' => $repo->isAdminThisGroup($member->user_id, $groupId),
                'is_joined' => $this->groupMemberRepository->isMember($groupId, $member->user_id, true),
                'is_requested' => $this->groupMemberRepository->isMember($groupId, $member->user_id, false)
            ];

            $member->offsetUnset('myGroups');
            foreach ($setAttributes as $attribute => $value) {
                $member->setAttribute($attribute, $value);
            }


            return $member;
        });

        $groupWithMembers->setCollection($groupMembers);

        return array_merge(
            [
                'members_count' => $groupMembersCount
            ],
            $groupWithMembers->toArray()
        );
    }

    /**
     * @param User $user
     * @param int $groupId
     * @param array $data
     */
    public function invite(User $user, int $groupId, array $data): void
    {
        if (Gate::denies(GatePrefixInterface::GROUP_INVITE_FRIENDS, $groupId)) {
            throw new BadRequestException(ExceptionMessageInterface::YOU_DONT_GROUP_MEMBER_MSG);
        }

        $this->sendInviteNotifyFriends($groupId, $data['friends']);
    }

    /**
     * @param int $groupId
     * @param iterable $recipients
     */
    private function sendInviteNotifyFriends(int $groupId, iterable $recipients): void
    {
        $data = [
            'group_id' => $groupId,
            'type' => Notification::STATUS_INVITED_GROUP
        ];

        foreach ($recipients as $recipientId) {
            /** @psalm-suppress UndefinedClass **/
            Notify::storeById($recipientId, $data);
        }
    }

    /**
     * @param User $user
     * @param GroupFilter $filter
     * @param int $perPage
     * @return CursorPaginator
     */
    public function search(User $user, GroupFilter $filter, int $perPage = 25): CursorPaginator
    {
        $select = array_merge($this->groupRepository::DEFAULT_SELECT, ['active', 'join_privacy']);

        /** @var CursorPaginator $groups */
        $groups = Group::filter($filter)
                        ->select($select)
                        ->with('catry')
                        ->withCount('members')
                        ->cursorPaginateExtended($perPage);

        $groups->each(fn (Group $group) => $this->setUserAttributes($group, $user->user_id));

        return $groups;
    }

    /**
     * @param Group $group
     * @param iterable $attributes
     * @return Group
     */
    private function offsetUnsetAttributes(Group $group, iterable $attributes): Group
    {
        foreach ($attributes as $attr) {
            $group->offsetUnset($attr);
        }

        return $group;
    }

    /**
     * @param User $user
     * @param int $perPage
     * @return CursorPaginator
     */
    public function getGroups(User $user, int $perPage): CursorPaginator
    {
        /** @var CursorPaginator $groups */
        $groups = $this->groupRepository->getGroups($perPage);
        /** @var GroupActionService $groupActionService */
        $groups->each(function (Group $group) use ($user): Group {
            $this->setUserAttributes($group, $user->user_id);

            return $this->offsetUnsetAttributes($group, ['registered', 'deleted_at']);
        });

        return $groups;
    }
}
