<?php

declare(strict_types=1);

namespace App\Services;

use App\Collections\BlogCollection;
use App\Models\Blog;
use App\Models\User;
use App\ProjectClass\ProjectCursorPaginator;
use App\Repositories\BlogBookmarkRepository;
use App\Repositories\BlogRepository;

/**
 * class BlogBookmarkService
 * @package App\Services
 */
class BlogBookmarkService
{

    public const STATUS_SAVED = true;
    public const STATUS_UNSAVED = false;

    /**
     * @var BlogBookmarkRepository $repository
     */
    private BlogBookmarkRepository $repository;

    /**
     * @var bool $saved
     */
    private bool $saved;

    public function __construct(BlogBookmarkRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param User $user
     * @param int $perPage
     * @return ProjectCursorPaginator
     */
    public function getBookmarks(User $user, int $perPage): ProjectCursorPaginator
    {
        $this->repository->setWith(array_merge(BlogBookmarkRepository::DEFAULT_WITH, ['blog:id,user,title,thumbnail,category,posted']));
        $bookmarksCursorPaginate = $this->repository->getBookmarks($user->user_id, $perPage);

        /** @var BlogCollection $blogs */
        $blogs = BlogCollection::make($bookmarksCursorPaginate->pluck('blog'));
        $blogs->each->setHidden(['user']);
        $blogs->setIsSavedAttribute($user->user_id);
        $bookmarksCursorPaginate->setCollection($blogs);

        return $bookmarksCursorPaginate;
    }

    /**
     * @param User $user
     * @param int $blogId
     * @return bool
     */
    public function toggle(User $user, int $blogId): bool
    {
        $article = resolve(BlogRepository::class)->getArticle($blogId);

        if ($article) {
            $isBookmarked = $this->repository->isBookmarked($user->user_id, $blogId);

            if ($isBookmarked) {
                $user->blogBookmarks()->detach($blogId);
                $this->saved = self::STATUS_UNSAVED;
            } else {
                $user->blogBookmarks()->attach($blogId);
                $this->saved = self::STATUS_SAVED;
            }
        }

        return $this->saved;
    }
}
