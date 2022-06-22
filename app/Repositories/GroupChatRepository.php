<?php

namespace App\Repositories;

use App\Models\GroupChat;
use App\Models\User;
use App\Repositories\Base\BaseModelRepository;
use Illuminate\Pagination\CursorPaginator;

class GroupChatRepository extends BaseModelRepository
{
    protected function getModel(): string
    {
        return GroupChat::class;
    }

    /**
     * @param string $search
     * @return CursorPaginator
     */
    public function search(string $search): CursorPaginator
    {
        return $this->getModelClone()
            ->newQuery()
            ->where('group_name', 'LIKE', "%{$search}%")
            ->cursorPaginateExtended();
    }

    /**
     * @param User $user
     * @return CursorPaginator
     */
    public function getGroupChats(User $user): CursorPaginator
    {
        return $user->groupChats()
            ->orderBy('time', 'DESC')
            ->with(['admins', 'users'])
            ->cursorPaginateExtended();
    }
}
