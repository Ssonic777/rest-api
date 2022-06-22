<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Blog;
use App\Models\Reaction;
use App\Models\User;
use App\Repositories\BlogRepository;

/**
 * class BlockdeskLikeService
 * @package App\Services
 */
class BlockdeskLikeService
{
    /**
     * @var BlogRepository $repository
     */
    private BlogRepository $repository;

    public function __construct(BlogRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param User $user
     * @param int $blogId
     * @return array
     */
    public function toggleLike(User $user, int $blogId): array
    {
        /** @var Blog $foundBlog */
        $foundBlog = $this->repository->find($blogId);

        if ($foundBlog->post->reactions->contains($user->user_id)) {
            $foundBlog->post->reactions()->detach($user->user_id);
            $message = 'Unliked';
        } else {
            $foundBlog->post->reactions()->attach($user->user_id, ['reaction' => Reaction::LOVE]);
            $message = 'Liked';
        }

        return [
            'message' => $message,
            'likes_count' => $foundBlog->post->refresh()->reactions->count(),
        ];
    }
}
