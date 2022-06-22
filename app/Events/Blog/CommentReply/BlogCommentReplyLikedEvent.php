<?php

declare(strict_types=1);

namespace App\Events\Blog\CommentReply;

use App\Models\CommentReply;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * class BlogCommentReplyLikedEvent
 * @package App\Events\Blog\CommentReply
 */
class BlogCommentReplyLikedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public int $likedUserId;

    public CommentReply $commentReply;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(CommentReply $commentReply, int $likedUserId)
    {
        $this->commentReply = $commentReply;
        $this->likedUserId = $likedUserId;
    }
}
