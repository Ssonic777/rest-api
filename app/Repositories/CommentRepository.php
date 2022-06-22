<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Comment;
use App\Repositories\Base\BaseModelRepository;
use App\ProjectClass\ProjectCursorPaginator;
use Illuminate\Pagination\CursorPaginator;

/**
 * class CommentRepository
 * @package App\Repositories
 */
class CommentRepository extends BaseModelRepository
{

    public const DEFAULT_SELECT = ['id', 'post_id', 'user_id', 'text', 'c_file', 'time'];

    public const DEFAULT_WITH = [
        'user:user_id,first_name,last_name,username,avatar',
        'replies.user:user_id,first_name,last_name,username,avatar',
        'replies:id,user_id,comment_id,text,c_file,time',
        'post:post_id,blog_id'
    ];

    public const DEFAULT_WITH_COUNT = ['reactions', 'replies'];

    protected function getModel(): string
    {
        return Comment::class;
    }

    protected function initializeDefaultData(): void
    {
        $this->setSelect(self::DEFAULT_SELECT);
        $this->setWith(self::DEFAULT_WITH);
        $this->setWithCount(self::DEFAULT_WITH_COUNT);
    }

    /**
     * @param int $postId
     * @param string $sortColumn
     * @param string $sortDirection
     * @param int $perPage
     * @return CursorPaginator
     */
    public function getCommentsByPostId(int $postId, string $sortColumn = 'id', string $sortDirection = 'ASC', int $perPage = 15): CursorPaginator
    {
        return $this->getModelClone()->newQuery()
                                    ->select($this->getSelect())
                                    ->where('post_id', '=', $postId)
                                    ->with($this->getWith())
                                    ->withCount(['reactions', 'replies'])
                                    ->orderBy($sortColumn, $sortDirection)
                                    ->cursorPaginateExtended($perPage);
    }

    /**
     * @param int $blogPostId
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function getBlogComments(int $blogPostId, int $perPage = null): ProjectCursorPaginator
    {
        return $this->getModelClone()->newQuery()
            ->select($this->getSelect())
            ->with($this->getWith())
            ->with('replies', function ($query): void {
                $query->withCount('reactions');
            })
            ->where('post_id', '=', $blogPostId)
            ->withCount($this->getWithCount())
            ->projectCursorPaginate($perPage);
    }

    /**
     * @param int $blogPostId
     * @param int $commentId
     * @return Comment
     */
    public function findBlogComment(int $blogPostId, int $commentId): Comment
    {
        return $this->getModelClone()->newQuery()
            ->select($this->getSelect())
            ->with($this->getWith())
            ->with('replies', function ($query): void {
                $query->withCount('reactions');
            })
            ->where('post_id', '=', $blogPostId)
            ->withCount($this->getWithCount())
            ->findOrFail($commentId);
    }

    /**
     * @param int $postId
     * @param int $commentId
     * @return Comment
     */
    public function findCommentByPostId(int $postId, int $commentId): Comment
    {
        return $this->getModelClone()->newQuery()
                                    ->with($this->getWith())
                                    ->select($this->getSelect())
                                    ->where('post_id', '=', $postId)
                                    ->withCount($this->getWithCount())
                                    ->findOrFail($commentId);
    }
}
