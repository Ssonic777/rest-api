<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\Contracts\ExceptionMessageInterface;
use App\Models\Group;
use App\Models\User;
use App\Repositories\GroupAdminRepository;
use App\Repositories\GroupRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * class GroupAdminService
 * @package App\Services
 */
class GroupAdminService
{
    /**
     * @var GroupAdminRepository $repository
     */
    public GroupAdminRepository $repository;

    /**
     * @var UserRepository $userRepository
     */
    public UserRepository $userRepository;

    /**
     * @var GroupRepository $groupRepository
     */
    public GroupRepository $groupRepository;

    public function __construct(GroupAdminRepository $repository, UserRepository $userRepository, GroupRepository $groupRepository)
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param int $groupId
     * @return CursorPaginator
     */
    public function getAdmins(int $groupId): array
    {
        /** @var GroupAdminRepository $repo */
        $repo = resolve(GroupAdminRepository::class);

        /** @var CursorPaginator $groupAdmins */
        $groupAdmins = $repo->getGroupAdmins($groupId);
        $groupAdmins->setCollection($groupAdmins->getCollection()->pluck('user'))->toArray();
        $groupAdminsCount = $repo->getGroupAdminsCount($groupId);

        return array_merge(
            [
                'admins_count' => $groupAdminsCount
            ],
            $groupAdmins->toArray()
        );
    }

    /**
     * @param int $groupId
     * @param int $userId
     * @param array $data
     * @return Model
     */
    public function storeGroupAdmin(int $groupId, int $userId, array $data = []): Model
    {
        $this->checkPermission('create', $groupId);
        $this->checkPermission('canAddGroupAdmin', $groupId, $userId);
        unset($data['group_id'], $data['user_id']);

        /** @var Group $foundGroup */
        $foundGroup = $this->groupRepository->find($groupId);
        $isAlreadyAdmin = app()->call(GroupAdminRepository::class . '@isAdminThisGroup', ['groupId' => $groupId, 'userId' => $userId]);

        if (!$isAlreadyAdmin) {
            $foundGroup->admins()->attach($userId, $data);
        }

        return $this->showGroupAdmin($groupId, $userId);
    }

    /**
     * @param int $groupId
     * @param int $userId
     * @return User
     */
    public function showGroupAdmin(int $groupId, int $userId): Model
    {
        $this->checkPermission('view', $groupId);

        return $this->repository->findGroupAdmin($groupId, $userId);
    }

    /**
     * @param int $groupId
     * @param int $userId
     * @param array $data
     * @return Model
     */
    public function updateGroupAdmin(int $groupId, int $userId, array $data): Model
    {
        $this->checkPermission('update', $groupId);
        $foundGroupAdmin = $this->repository->findGroupAdmin($groupId, $userId);
        $foundGroupAdmin->update($data);

        return $foundGroupAdmin->refresh();
    }

    /**
     * @param int $groupId
     * @param int $userId
     * @return array
     */
    public function deleteGroupAdmin(int $groupId, int $userId): array
    {
        $this->checkPermission('delete', $groupId);
        $this->repository->findGroupAdmin($groupId, $userId)->delete();

        return ['user_id' => $userId];
    }

    /**
     * @param string $ability
     * @param int ...$ids
     */
    private function checkPermission(string $ability, int ...$ids): void
    {
        if (Gate::denies($ability, array_merge([$this->repository->model->getMorphClass()], $ids))) {
            if ($ability == 'canAddGroupAdmin') {
                throw new BadRequestException(ExceptionMessageInterface::USER_DONT_GROUP_MEMBER_MSG);
            }

            throw new BadRequestException(ExceptionMessageInterface::DONT_RIGHT_MSG);
        }
    }
}
