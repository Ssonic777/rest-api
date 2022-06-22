<?php

declare(strict_types=1);

namespace App\Collections;

use App\Handlers\Blog\Comment\BlogCommentSetIsLikedAttributeHandler;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

/**
 * class CommentCollection
 * @package App\Collections
 */
class CommentCollection extends Collection
{
    /**
     * @param int $userId
     * @return $this
     */
    public function setIsLikedAttributes(int $userId): self
    {
        $this->each(function (Comment $comment) use ($userId): void {
            BlogCommentSetIsLikedAttributeHandler::execute($comment, $userId);
            $comment->setHidden(['user_id']);
            $comment->replies->setIsLikedAttributes($userId);
        });

        return $this;
    }
}
