<?php

declare(strict_types=1);

namespace App\Events\Follow;

use App\Models\Follower;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * class FollowedEvent
 * @package App\Events\Follow
 */
class FollowedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Follower $follower;
    public string $followerUsername;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Follower $follower, string $followerUsername)
    {
        $this->follower = $follower;
        $this->followerUsername = $followerUsername;
    }
}
