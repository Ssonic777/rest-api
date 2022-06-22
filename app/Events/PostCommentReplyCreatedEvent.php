<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\CommentReply;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * class PostCommentReplyCreatedEvent
 * @package App\Events
 */
class PostCommentReplyCreatedEvent
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
