<?php

namespace App\Search;

use App\Models\User;
use App\Models\Follower;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\CursorPaginator;

class UserSearch
{
    /**
     * @var User|Builder
     */
    private $user;
    /**
     * @var Request
     */
    private $filters;

    public function __construct()
    {
        $this->user = resolve(User::class)->newQuery();
    }

    /**
     * @param Request $filters
     */
    public function apply(Request $filters): void
    {
        $this->filters = $filters;

        $this->whereLikeFilter('username');
        $this->whereLikeFilter('first_name');
        $this->whereLikeFilter('last_name');
        $this->whereFilter('admin');
        $this->whereLikeFilter('email');
        $this->whereFilter('user_id');
        $this->withFollowing();
    }

    /**
     * @param int|null $pagination
     * @return User[]|CursorPaginator|Builder[]|Collection
     */
    public function find(?int $pagination = null)
    {
        if ($pagination) {
            return $this->user->cursorPaginateExtended($pagination);
        }

        return $this->user->get();
    }

    /**
     * @param $filteredField
     */
    private function whereFilter($filteredField)
    {
        if ($this->filters->has($filteredField)) {
            $this->user->where($filteredField, $this->filters->input($filteredField));
        }
    }

    /**
     * @param $filteredField
     */
    private function whereLikeFilter($filteredField)
    {
        if ($this->filters->has($filteredField)) {
            $this->user->where($filteredField, $this->filters->input($filteredField))
                ->orWhere($filteredField, 'like', $this->filters->input($filteredField) . '%');
        }
    }

    /**
     *
     */
    private function withFollowing()
    {
        $followersTable = (new Follower())->getTable();

        if ($this->filters->has('isFollowing')) {
            $this->user->join(
                $followersTable,
                'Wo_Users.user_id',
                '=',
                'Wo_Followers.following_id'
            )->where('Wo_Followers.following_id', '=', 'Wo_Users.user_id');
        }
    }
}
