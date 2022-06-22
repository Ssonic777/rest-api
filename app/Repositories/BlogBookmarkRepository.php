<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\BlogBookmark;
use App\Repositories\Base\BaseModelRepository;
use App\ProjectClass\ProjectCursorPaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * class BlogBookmarkRepository
 * @package App\Repositories
 */
class BlogBookmarkRepository extends BaseModelRepository
{
    public const DEFAULT_WITH = [
        'blog.owner:user_id,first_name,last_name,avatar',
        'blog.catry.title:lang_key,english'
    ];

    protected function getModel(): string
    {
        return BlogBookmark::class;
    }

    protected function initializeDefaultData(): void
    {
        $this->setWith(self::DEFAULT_WITH);
    }

    /**
     * @param int $userId
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function getBookmarks(int $userId, int $perPage = null): ProjectCursorPaginator
    {
        return $this->getModelClone()->newQuery()
                                    ->with($this->getWith())
                                    ->where([
                                        ['user_id', '=', $userId]
                                    ])
                                    ->whereHas('blog', function (Builder $query): void {
                                        $query->whereNull('deleted_at');
                                    })
                                    ->orderByDesc('time')
                                    ->projectCursorPaginate($perPage, 'blog_id');
    }

    /**
     * @param int $userId
     * @param int $blogId
     * @return bool
     */
    public function isBookmarked(int $userId, int $blogId): bool
    {
        return $this->getModelClone()->newQuery()
                                    ->where([
                                        ['user_id', '=', $userId],
                                        ['blog_id', '=', $blogId]
                                    ])
                                    ->exists();
    }
}
