<?php

declare(strict_types=1);

namespace App\Handlers\User\Attributes;

use App\Models\Post;

/**
 * class UserSetIsPinnedAttribute
 * @package App\Handlers\User\Attributes
 */
class UserSetIsPinnedAttribute
{
    /**
     * @param Post $post
     * @return Post
     */
    public static function execute(Post $post): Post
    {
        $post->setAttribute('is_pinned', $post->pin()->exists());

        return $post;
    }
}
