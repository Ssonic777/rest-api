<?php

declare(strict_types=1);

namespace App\Collections;

use App\Handlers\Blog\CommentReply\BlogCommentReplySetIsLikedAttributeHandler;
use App\Models\CommentReply;
use Illuminate\Database\Eloquent\Collection;

/**
 * class CommentReplyCollect
 * @package App\Collections
 */
class CommentReplyCollection extends Collection
{
    /**
     * @param int $userId
     * @return $this
     */
    public function setIsLikedAttributes(int $userId): self
    {
        $this->each(function (CommentReply $commentReply) use ($userId): void {
            BlogCommentReplySetIsLikedAttributeHandler::execute($commentReply, $userId);
            $commentReply->setHidden(['user_id', 'page_id', 'edited']);
        });

        return $this;
    }
}
