<?php

declare(strict_types=1);

namespace App\Events\Post;

use App\Models\Post;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * class PostReportedEvent
 * @package Aoo\Events\Post
 */
class PostRepostedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Post $reportedPost;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Post $reportedPost)
    {
        $this->reportedPost = $reportedPost;
    }
}
