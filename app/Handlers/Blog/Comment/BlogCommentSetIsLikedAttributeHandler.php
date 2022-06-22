<?php

declare(strict_types=1);

namespace App\Handlers\Blog\Comment;

use App\Models\Comment;

/**
 * class BlogCommentSetIsLikedAttributeHandler
 * @package App\Handlers\Blog\Comment
 */
class BlogCommentSetIsLikedAttributeHandler
{
    /**
     * @param Comment $comment
     * @param int $userId
     * @return Comment
     */
    public static function execute(Comment $comment, int $userId): Comment
    {
        $comment->setAttribute('is_liked', $comment->reactions()->wherePivot('user_id', $userId)->exists());

        return $comment;
    }
}
