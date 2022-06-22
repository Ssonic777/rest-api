<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\BlogCommentReply;
use App\Repositories\Base\BaseModelRepository;
use Illuminate\Pagination\CursorPaginator;

/**
 * class BlogCommentReplyRepository
 * @package App\Repositories
 */
class BlogCommentReplyRepository extends BaseModelRepository
{

    public const DEFAULT_WITH = [
        'user:user_id,first_name,last_name,avatar'
    ];

    /**
     * @return string
     */
    protected function getModel(): string
    {
        return BlogCommentReply::class;
    }

    protected function initializeDefaultData(): void
    {
        $this->setWith(self::DEFAULT_WITH);
    }

    /**
     * @param int $blogId
     * @param int $commentId
     * @return CursorPaginator
     */
    public function getCommentReplies(int $blogId, int $commentId): CursorPaginator
    {
        return $this->getModelClone()->newQuery()
                                    ->where([
                                        ['blog_id', '=', $blogId],
                                        ['comm_id', '=', $commentId]
                                    ])
                                    ->with($this->getWith())
                                    ->cursorPaginateExtended();
    }

    /**
     * @param int $blogId
     * @param int $commentId
     * @param int $commentReplyId
     * @return BlogCommentReply
     */
    public function findCommentReply(int $blogId, int $commentId, int $commentReplyId): BlogCommentReply
    {
        return $this->getModelClone()->newQuery()
                                    ->where([
                                        ['blog_id', '=', $blogId],
                                        ['comm_id', '=', $commentId]
                                    ])
                                    ->with($this->getWith())
                                    ->findOrFail($commentReplyId);
    }
}
