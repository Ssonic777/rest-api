<?php

declare(strict_types=1);

namespace App\Events\Blog\CommentReply;

use App\Models\CommentReply;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * class BlogCommentReplyCreatedEvent
 * @package App\Events\Blog\CommentReply
 */
class BlogCommentReplyCreatedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var CommentReply $commentReply
     */
    public CommentReply $commentReply;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(CommentReply $commentReply)
    {
        $this->commentReply = $commentReply;
    }
}
