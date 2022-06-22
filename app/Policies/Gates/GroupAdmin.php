<?php

declare(strict_types=1);

namespace App\Policies\Gates;

use App\Models\User;
use App\Repositories\GroupAdminRepository;
use App\Repositories\GroupRepository;

/**
 * class GroupAdmin
 * @package App\Policies\Gates
 */
class GroupAdmin
{

    /**
     * @var GroupRepository $groupRepository
     */
    private GroupRepository $groupRepository;

    /**
     * @var GroupAdminRepository $repository
     */
    private GroupAdminRepository $repository;

    public function __construct(GroupRepository $groupRepository, GroupAdminRepository $repository)
    {
        $this->groupRepository = $groupRepository;
        $this->repository = $repository;
    }

    /**
     * @param User $user
     * @param string $ability
     * @param int $groupId
     * @return bool|null
     */
    public function before(User $user, string $ability, int $groupId): ?bool
    {
        return $this->groupRepository->isAuthorThisGroup($user->user_id, $groupId) ?: null;
    }

    /**
     * @param User $user
     * @param int $groupId
     * @return bool
     */
    public function isGroupAdmin(User $user, int $groupId): bool
    {
        return $this->repository->isAdminThisGroup($user->user_id, $groupId);
    }
}
