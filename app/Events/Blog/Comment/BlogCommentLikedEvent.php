<?php

declare(strict_types=1);

namespace App\Events\Blog\Comment;

use App\Models\Comment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * class BlogCommentCreatedEvent
 * @package App\Events\Blog\Comment
 */
class BlogCommentLikedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Comment $comment;
    public int $likedUserId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Comment $comment, int $likedUserId)
    {
        $this->comment = $comment;
        $this->likedUserId = $likedUserId;
    }
}
