<?php

declare(strict_types=1);

namespace App\Policies\Gates;

use App\Models\Post;
use App\Models\User;

/**
 * class PostGate
 * @package App\Polices\Gates
 */
class PostGate
{
    /**
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function hide(User $user, Post $post): bool
    {
        return $user->user_id != $post->user_id;
    }
}
