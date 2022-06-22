<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\GroupMember;
use App\Repositories\Base\BaseModelRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Pagination\CursorPaginator;

/**
 * class GroupMemberRepository
 * @package App\Repositories;
 * @property GroupMember $model
 */
class GroupMemberRepository extends BaseModelRepository
{

    public function getModel(): string
    {
        return GroupMember::class;
    }

    /**
     * @param int $groupId
     * @param int $userId
     * @param bool $checkActivated
     * @return bool
     */
    public function isMember(int $groupId, int $userId, ?bool $checkActivated = null): bool
    {
        return $this->getModelClone()->newQuery()->where([
                                                    ['user_id', '=', $userId],
                                                    ['group_id', '=', $groupId],
                                                ])
                                                ->when(
                                                    is_bool($checkActivated),
                                                    function (Builder $query) use ($checkActivated): void {
                                                        $query->when(
                                                            $checkActivated,
                                                            function (Builder $query): void {
                                                                $query->where('active', '=', $this->model::ACTIVE);
                                                            },
                                                            function (Builder $query): void {
                                                                $query->where('active', '=', $this->model::NOT_ACTIVE);
                                                            }
                                                        );
                                                    }
                                                )
                                                ->exists();
    }

    /**
     * @param int $userId
     * @param string|null $active
     * @param int|null $perPage
     * @return CursorPaginator
     */
    public function getUserGroups(int $userId, ?string $active = null, int $perPage = null): CursorPaginator
    {
        return $this->getModelClone()->newQuery()->where('user_id', '=', $userId)
                                                ->select($this->getSelect())
                                                ->when($active, function (Builder $query) use ($active): void {
                                                    $query->where('active', '=', $active);
                                                })
                                                ->whereHas('group', fn (Builder $query): Builder => $query->whereNull('deleted_at'))
                                                ->with(['group' => function (BelongsTo $query): void {
                                                    $query->select(['id', 'user_id', 'group_name', 'group_title', 'avatar', 'cover', 'privacy', 'category']);
                                                    $query->with('catry');
                                                    $query->withCount('members');
                                                }])
                                                ->cursorPaginateExtended($perPage);
    }

    /**
     * @param int $groupId
     * @param bool|null $checkActivated
     * @param int|null $perPage
     * @return CursorPaginator
     */
    public function getGroupMembers(int $groupId, ?bool $checkActivated = null, int $perPage = null): CursorPaginator
    {
        return $this->getModelClone()->newQuery()->where('group_id', '=', $groupId)
                                                    ->orderByDesc('user_id')
                                                    ->when(
                                                        is_bool($checkActivated),
                                                        function (Builder $query) use ($checkActivated): void {
                                                            $query->when(
                                                                $checkActivated,
                                                                function (Builder $query): void {
                                                                    $query->where('active', '=', $this->model::ACTIVE);
                                                                },
                                                                function (Builder $query): void {
                                                                    $query->where('active', '=', $this->model::NOT_ACTIVE);
                                                                }
                                                            );
                                                        }
                                                    )
                                                ->with('member:user_id,username,first_name,last_name,avatar,email')
                                                ->cursorPaginateExtended($perPage);
    }

    /**
     * @param int $groupId
     * @param bool|null $checkActivated
     * @return int
     */
    public function getGroupMembersCont(int $groupId, ?bool $checkActivated = null): int
    {
        return $this->getModelClone()->newQuery()->where('group_id', '=', $groupId)
                                                ->when(
                                                    is_bool($checkActivated),
                                                    function (Builder $query) use ($checkActivated): void {
                                                        $query->when(
                                                            $checkActivated,
                                                            function (Builder $query): void {
                                                                $query->where('active', '=', $this->model::ACTIVE);
                                                            },
                                                            function (Builder $query): void {
                                                                $query->where('active', '=', $this->model::NOT_ACTIVE);
                                                            }
                                                        );
                                                    }
                                                )
                                                ->count();
    }
}
