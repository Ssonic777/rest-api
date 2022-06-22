<?php

declare(strict_types=1);

namespace App\Handlers\User\Attributes;

use App\Models\Post;

/**
 * class UserSetIsLikedAttribute
 * @package App\Handlers\User\Attributes
 */
class UserSetIsLikedAttribute
{
    /**
     * @param Post $post
     * @param int $userId
     * @return Post
     */
    public static function execute(Post $post, int $userId): Post
    {
        $isLiked = $post->reactions()->wherePivot('user_id', $userId)->exists();
        $post->setAttribute('is_liked', $isLiked);

        return $post;
    }
}
