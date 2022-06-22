<?php

declare(strict_types=1);

namespace App\Handlers\Blog\CommentReply;

use App\Models\CommentReply;

/**
 * class BlogCommentReplySetIsLikedAttributeHandler
 * @package  App\Handlers\Blog\CommentReply
 */
class BlogCommentReplySetIsLikedAttributeHandler
{
    /**
     * @param CommentReply $commentReply
     * @param int $userId
     * @return CommentReply
     */
    public static function execute(CommentReply $commentReply, int $userId): CommentReply
    {
        $commentReply->setAttribute('is_liked', $commentReply->reactions()->wherePivot('user_id', '=', $userId)->exists());

        return $commentReply;
    }
}
