<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AdminGroup;
use App\Models\GroupAdmin;
use App\Models\User;
use App\Repositories\GroupMemberRepository;
use App\Repositories\GroupRepository;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * class GroupAdminPolicy
 * @package App\Policies
 */
class GroupAdminPolicy
{
    use HandlesAuthorization;

    /**
     * @var GroupRepository $repository
     */
    private GroupRepository $repository;

    public function __construct(GroupRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  User  $user
     * @return mixed
     */
    public function viewAny(User $user): void
    {
        //
    }

    /**
     * @param User $user
     * @param int $groupId
     * @return bool
     */
    public function view(User $user, int $groupId): bool
    {
        return $this->repository->isAuthorThisGroup($user->user_id, $groupId);
    }

    /**
     * @param User $user
     * @param int $groupId
     * @return bool
     */
    public function create(User $user, int $groupId): bool
    {
        return $this->repository->isAuthorThisGroup($user->user_id, $groupId);
    }

    /**
     * @param User $user
     * @param int $groupId
     * @return bool
     */
    public function update(User $user, int $groupId): bool
    {
        return $this->repository->isAuthorThisGroup($user->user_id, $groupId);
    }

    /**
     * @param User $user
     * @param int $groupId
     * @return bool
     */
    public function delete(User $user, int $groupId): bool
    {
        return $this->repository->isAuthorThisGroup($user->user_id, $groupId);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  GroupAdmin  $groupAdmin
     * @return mixed
     */
    public function restore(User $user, GroupAdmin $groupAdmin): void
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  GroupAdmin $groupAdmin
     * @return mixed
     */
    public function forceDelete(User $user, GroupAdmin $groupAdmin): void
    {
        //
    }

    /**
     * @param User $user
     * @param int $groupId
     * @param int $userId
     * @return bool
     */
    public function canAddGroupAdmin(User $user, int $groupId, int $userId): bool
    {
        /** @var GroupMemberRepository $repo */
        $repo = resolve(GroupMemberRepository::class);

        return $repo->isMember($groupId, $userId, true);
    }
}
