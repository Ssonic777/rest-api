<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\CommentReply;
use App\ProjectClass\ProjectCursorPaginator;
use App\Repositories\Base\BaseModelRepository;

/**
 * Class CommentReplyRepository
 * @package App\Repositories
 */
class CommentReplyRepository extends BaseModelRepository
{

    public const DEFAULT_SELECT = [
        'id',
        'user_id',
        'comment_id',
        'text',
        'c_file',
        'time',
    ];

    public const DEFAULT_WITH = [
        'user:user_id,first_name,last_name,username,avatar',
    ];

    public const DEFAULT_WITH_COUNT = [
        'reactions',
    ];

    /**
     * @return string
     */
    protected function getModel(): string
    {
        return CommentReply::class;
    }

    protected function initializeDefaultData(): void
    {
        $this->setSelect(self::DEFAULT_SELECT);
        $this->setWith(self::DEFAULT_WITH);
        $this->setWithCount(self::DEFAULT_WITH_COUNT);
    }

    /**
     * @param int $commentId
     * @param int $replyId
     * @return CommentReply
     */
    public function findCommentReply(int $commentId, int $replyId): CommentReply
    {
        return $this->getModelClone()->newQuery()
                                ->select($this->getSelect())
                                ->with($this->getWith())
                                ->where('comment_id', $commentId)
                                ->withCount($this->getWithCount())
                                ->findOrFail($replyId);
    }

    /**
     * @param int $commentId
     * @return ProjectCursorPaginator
     */
    public function getRepliesByCommentId(int $commentId): ProjectCursorPaginator
    {
        return $this->getModelClone()->newQuery()
                                    ->where('comment_id', $commentId)
                                    ->projectCursorPaginate();
    }

    /**
     * @param int $commentId
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function getBlogCommentReplies(int $commentId, int $perPage = null): ProjectCursorPaginator
    {
        return $this->getModelClone()->newQuery()
            ->select($this->getSelect())
            ->where([
                ['comment_id', '=', $commentId]
            ])
            ->with($this->getWith())
            ->withCount($this->getWithCount())
            ->projectCursorPaginate($perPage);
    }
}
