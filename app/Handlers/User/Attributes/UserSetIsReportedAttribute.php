<?php

declare(strict_types=1);

namespace App\Handlers\User\Attributes;

use App\Models\Post;

/**
 * class UserSetIsReportedAttribute
 * @package App\Handlers\User\Attributes
 */
class UserSetIsReportedAttribute
{
    /**
     * @param Post $post
     * @param int $userId
     * @return Post
     */
    public static function execute(Post $post, int $userId): Post
    {
        $isReported = $post->reports()->wherePivot('user_id', $userId)->exists();
        $post->setAttribute('is_reported', $isReported);

        return $post;
    }
}
