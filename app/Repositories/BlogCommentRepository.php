<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\BlogComment;
use App\Repositories\Base\BaseModelRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;

/**
 * class BlogCommentRepository
 * @package App\Repositories
 */
class BlogCommentRepository extends BaseModelRepository
{

    public const DEFAULT_WITH = [
        'user:user_id,first_name,last_name,avatar'
    ];

    protected function getModel(): string
    {
        return BlogComment::class;
    }

    protected function initializeDefaultData(): void
    {
        $this->setWith(self::DEFAULT_WITH);
    }

    /**
     * @param int $blogId
     * @param int $perPage
     * @return CursorPaginator
     */
    public function getComments(int $blogId, int $perPage = 15): CursorPaginator
    {
        return $this->getModelClone()->newQuery()
                                    ->with($this->getWith())
                                    ->where('blog_id', $blogId)
                                    ->cursorPaginateExtended($perPage);
    }

    /**
     * @param int $blogId
     * @param int $commentId
     * @return BlogComment
     */
    public function findBlogComment(int $blogId, int $commentId): BlogComment
    {
        return $this->getModelClone()->newQuery()
                                    ->with($this->getWith())
                                    ->where('blog_id', '=', $blogId)
                                    ->findOrFail($commentId);
    }
}
