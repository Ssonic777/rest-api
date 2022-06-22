<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Group;
use App\Models\User;
use App\Repositories\GroupAdminRepository;
use App\Repositories\GroupRepository;
use App\Services\ServiceHandlers\GroupServiceHandler;
use App\Traits\FileTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * class GroupService
 * @package App\Services
 */
class GroupService
{
    use FileTrait;

    /**
     * @var array|string[] $with
     */
    private array $with = [
        'catry',
        'setting'
    ];

    /**
     * @var array $groupImages
     */
    private array $groupImages = [
        'avatar' => Group::GROUP_AVATAR,
        'cover' => Group::GROUP_COVER
    ];

    /**
     * @var GroupServiceHandler $handler
     */
    private GroupServiceHandler $handler;

    /**
     * @var GroupRepository $repository
     */
    public GroupRepository $repository;

    /**
     * @var GroupAdminRepository $groupAdminRepository
     */
    public GroupAdminRepository $groupAdminRepository;

    public function __construct(GroupServiceHandler $handler, GroupRepository $repository, GroupAdminRepository $groupAdminRepository)
    {
        $this->handler = $handler;
        $this->repository = $repository;
        $this->groupAdminRepository = $groupAdminRepository;
    }

    /**
     * @param User $user
     * @param array $data
     * @return Model
     */
    public function storeGroup(User $user, array $data): Model
    {
        $this->handler->checkPermission->execute('create', $this->repository->model);
        $this->changePrivacy($data);

        $storedGroup = $user->myGroups()->create(
            array_merge($data, [
                'group_name' => $data['group_slug'],
                'join_privacy' => $data['privacy']
            ])
        );

        return $storedGroup->fresh($this->with);
    }

    /**
     * @param User $user
     * @param int $id
     * @return Model
     */
    public function showGroup(User $user, int $id): Model
    {
        $this->repository->setSelect(
            array_merge(
                $this->repository->getSelect(),
                ['active']
            )
        )
        ->setWith($this->with);

        /** @var Group $foundGroup */
        $foundGroup = $this->repository->showGroup($id);
        /** @var GroupActionService $groupActionService */
        $groupActionService = resolve(GroupActionService::class);
        $groupActionService->setUserAttributes($foundGroup, $user->user_id);

        return $this->setAdminPermissions($foundGroup, $user->user_id);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function updateGroup(int $id, array $data): Model
    {
        /** @var Group $foundGroup */
        $foundGroup = $this->repository->find($id);
        $this->handler->checkPermission->execute('update', $foundGroup);
        $this->changePrivacy($data);

        $data = array_key_exists('group_slug', $data)   ? array_merge($data, ['group_name' => $data['group_slug']])
                                                            : $data;
        $foundGroup->update($data);
        $this->handler->modelDeleteAttributes->execute(
            $foundGroup->fresh($this->with),
            $this->handler->modelAttributes->setAttributes(['registered', 'deleted_at'])
        );

        return $foundGroup;
    }

    /**
     * @param int $id
     */
    public function deleteGroup(int $id): void
    {
        /** @var Group $foundGroup */
        $foundGroup = $this->repository->find($id);
        $this->handler->checkPermission->execute('delete', $foundGroup);

        foreach (array_keys($this->groupImages) as $value) {
            if ($foundGroup->$value) {
                $this->deleteFile($this->groupImages[$value], $foundGroup->$value, 'public');
            }
        }

        $foundGroup->delete();
    }

    /**
     * @param array $data
     * @return array
     */
    private function changePrivacy(array &$data): array
    {
        $privacies = [
            'privacy' => $this->repository->model::PRIVACIES,
            'join_privacy' => $this->repository->model::JOIN_PRIVACIES
        ];

        foreach ($privacies as $key => &$value) {
            if (array_key_exists($key, $data)) {
                $data[$key] = $data[$key] ? $value[0] : $value[1];
            }
        }

        return $data;
    }

    /**
     * @param Group $group
     * @param int $userId
     * @return Group
     */
    private function setAdminPermissions(Group $group, int $userId): Group
    {
        if ($group->getAttributeValue('is_admin') && $group->getAttributeValue('user_id')) {
            $permissions = $this->groupAdminRepository->findGroupAdmin($group->id, $userId);
            $group->setRelation('admin_permissions', $permissions);
        }

        return $group;
    }
}
