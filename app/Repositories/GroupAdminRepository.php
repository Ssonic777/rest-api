<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\GroupAdmin;
use App\Repositories\Base\BaseModelRepository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Pagination\CursorPaginator;

/**
 * class GroupAdminRepository
 * @package App\Repositories
 */
class GroupAdminRepository extends BaseModelRepository
{
    protected function getModel(): string
    {
        return GroupAdmin::class;
    }

    /**
     * @param int $groupId
     * @param int $userId
     * @return GroupAdmin
     */
    public function findGroupAdmin(int $groupId, int $userId): GroupAdmin
    {
        return $this->getModelClone()->newQuery()
                                    ->select($this->getSelect())
                                    ->where([
                                        ['group_id', '=', $groupId],
                                        ['user_id', '=', $userId],
                                    ])->firstOrFail();
    }

    /**
     * @param int $userId
     * @param int $groupId
     * @return bool
     */
    public function isAdminThisGroup(int $userId, int $groupId): bool
    {
        return $this->getModelClone()->newQuery()
                                    ->where([
                                        ['group_id', '=', $groupId],
                                        ['user_id', '=', $userId],
                                    ])
                                    ->exists();
    }

    /**
     * @param int $groupId
     * @param int $perPage
     * @return CursorPaginator
     */
    public function getGroupAdmins(int $groupId, int $perPage = 15): CursorPaginator
    {
        return $this->getModelClone()->newQuery()->select(['user_id'])
                                                ->where('group_id', '=', $groupId)
                                                ->with([
                                                    'user' => fn (BelongsTo $query): BelongsTo => $query->select(['user_id', 'username', 'first_name', 'last_name', 'avatar'])
                                                ])
                                                ->cursorPaginateExtended($perPage);
    }

    /**
     * @param int $groupId
     * @return int
     */
    public function getGroupAdminsCount(int $groupId): int
    {
        return $this->getModelClone()->newQuery()->where('group_id', '=', $groupId)
                                                ->count();
    }
}
