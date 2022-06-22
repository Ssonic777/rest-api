<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\CommentLike;
use App\Repositories\Base\BaseModelRepository;

/**
 * class CommentLikeRepository
 * @package App\Models\Repositories
 */
class BlockdeskCommentLikeRepository extends BaseModelRepository
{
    protected function getModel(): string
    {
        return CommentLike::class;
    }

    /**
     * @param int $articleId
     * @param int $userId
     * @param int $commentId
     * @return bool
     */
    public function isLiked(int $articleId, int $userId, int $commentId): bool
    {
        return $this->getModelClone()->newQuery()
                                    ->where([
                                        ['post_id', '=', $articleId],
                                        ['comment_id', '=', $commentId],
                                        ['user_id', '=', $userId],
                                    ])
                                    ->exists();
    }

    /**
     * @param int $userId
     * @param int $articleId
     * @param int $commentId
     * @return CommentLike|null
     */
    public function findArticleCommentLike(int $userId, int $articleId, int $commentId): ?CommentLike
    {
        return $this->getModelClone()->newQuery()
                                    ->where([
                                        ['post_id', '=', $articleId],
                                        ['comment_id', '=', $commentId],
                                        ['user_id', '=', $userId],
                                    ])
                                    ->first();
    }
}
