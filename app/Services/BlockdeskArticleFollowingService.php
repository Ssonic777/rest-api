<?php

declare(strict_types=1);

namespace App\Services;

use App\ProjectClass\ProjectCursorPaginator;
use App\Repositories\BlogRepository;
use Illuminate\Pagination\CursorPaginator;
use App\Models\User;
use App\Collections\BlogCollection;

/**
 * Class BlockdeskArticleFollowingService
 * @package App\Services
 */
class BlockdeskArticleFollowingService
{
    /**
    * @var BlogRepository $repository
    */
    public BlogRepository $repository;

    /**
     * @param BlogRepository $repository
     */
    public function __construct(BlogRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param User     $user
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function getBlockdeskArticleFollowing(User $user, int $perPage = null): ProjectCursorPaginator
    {
        $followers = $user->followings()->pluck('user_id');
        /** @var ProjectCursorPaginator $articles */
        $articles = $this->repository->getBlockdeskArticleFollowing($followers->toArray(), $perPage);
        $articles->setIsSavedAttribute($user->user_id);

        return $articles;
    }
}
