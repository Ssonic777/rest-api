<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PinnedPost;
use App\Repositories\PostRepository;

/**
 * class PostPinService
 * @package App\Services
 */
class PostPinService
{
    public PostRepository $repository;

    /**
     * @param PostRepository $repository
     */
    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $postId
     * @param int $userId
     * @return array
     */
    public function pinToggle(int $postId, int $userId): array
    {
        $post = $this->repository->findUserPost($postId, $userId);

        if ($post->pin()->doesntExist()) {
            $post->pin()->create([
                'post_id' => $postId,
                'user_id' => $userId,
                'active' => PinnedPost::STATUS_ACTIVE,
            ]);
            $message = 'Pinned';
        } else {
            $post->pin()->delete();
            $message = 'Unpinned';
        }

        return [
            'message' => $message,
        ];
    }
}
