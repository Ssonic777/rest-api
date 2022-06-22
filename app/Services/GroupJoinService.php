<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\Contracts\ExceptionMessageInterface;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use App\Policies\Gates\Contracts\GatePrefixInterface;
use App\Repositories\GroupMemberRepository;
use App\Repositories\GroupRepository;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * class GroupJonService
 * @package App\Services
 */
class GroupJoinService
{

    /**
     * @var GroupRepository $groupRepository
     */
    public GroupRepository $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param User $user
     * @param int $groupId
     * @return array
     */
    public function joinToggle(User $user, int $groupId): array
    {
        $result = [];

        /** @var Group $foundGroup */
        $foundGroup = $this->groupRepository->find($groupId, ['id', 'user_id', 'join_privacy']);
        $isJoinedMember = $foundGroup->members()->wherePivot('user_id', $user->user_id)->exists();

        if ($isJoinedMember) {
            if ($this->groupRepository->isAuthorThisGroup($user->user_id, $groupId)) {
                throw new BadRequestException(ExceptionMessageInterface::DONT_LEFT_OWNER_GROUP);
            }

            $user->groups()->detach($groupId);
            $result['message'] = 'left';
        } else {
            $data = [
                'time' => time(),
                'active' => $foundGroup->isJoinPrivacy() ? GroupMember::NOT_ACTIVE : GroupMember::ACTIVE
            ];

            $user->groups()->syncWithPivotValues($groupId, $data, false);

            $result['message'] = $foundGroup->isJoinPrivacy() ? 'requested' : 'joined';
        }

        return $result;
    }

    /**
     * @param int $groupId
     * @param int $perPage
     * @return array
     */
    public function requests(int $groupId, int $perPage): array
    {
        $members = $this->groupRepository->getMembers($groupId, GroupMember::NOT_ACTIVE, $perPage);
        $membersCount = $this->groupRepository->getMembersCont($groupId, GroupMember::NOT_ACTIVE);

        return array_merge(['request_members_count' => $membersCount], $members->toArray());
    }

    /**
     * @param int $groupId
     * @param int $userId
     * @param array $validated
     * @return array
     */
    public function requestAnswer(int $groupId, int $userId, array $validated): array
    {

        if (Gate::denies(GatePrefixInterface::IS_GROUP_ADMIN, $groupId)) {
            throw new BadRequestException(ExceptionMessageInterface::DONT_RIGHT_MSG);
        }

        /** @var GroupMemberRepository $repo */
        $repo = resolve(GroupMemberRepository::class);
        $isRequested = $repo->isMember($groupId, $userId, false);

        if ($isRequested) {
            $this->requestAnswerAction($groupId, $userId, $validated);
        } else {
            $validated['request'] = $repo->isMember($groupId, $userId, true) ? 'user already member' : 'user did\'t request';
        }

        return array_merge(['user_id' => $userId], $validated);
    }

    /**
     * @param string $request
     * @return bool
     */
    private function isRequestAccepted(string $request): bool
    {
        return $request == GroupMember::REQUEST_ACCEPT;
    }

    /**
     * @param int $groupId
     * @param int $userId
     * @param array $validated
     * @return array|string
     */
    private function requestAnswerAction(int $groupId, int $userId, array $validated): void
    {
        $this->isRequestAccepted($validated['request']) ? $this->groupRepository->requestAccept($groupId, $userId)
                                                        : $this->groupRepository->requestDecline($groupId, $userId);
    }
}
