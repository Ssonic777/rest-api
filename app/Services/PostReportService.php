<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\PostRepository;

/**
 * class PostReportService
 * @package App\Services
 */
class PostReportService
{
    private PostRepository $repository;

    /**
     * @param PostRepository $repository
     */
    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param User $user
     * @param array $data
     * @return string[]
     */
    public function report(User $user, array $data): array
    {
        ['post_id' => $postId, 'text' => $text] = $data;
        $post = $this->repository->showPost($postId);
        $isReported = $post->reports()->wherePivot('user_id', $user->user_id)->exists();

        if ($isReported) {
            $status = 'You have reported the post already';
        } else {
            $user->postReports()->attach(['post_id' => $post->post_id], ['text' => $text]);
            $status = 'Reported';
        }

        return ['message' => $status];
    }

    /**
     * @param User $user
     * @param int $postId
     * @return string[]
     */
    public function withdraw(User $user, int $postId): array
    {
        $post = $this->repository->showPost($postId);
        $isReported = $post->reports()->wherePivot('user_id', $user->user_id)->exists();

        if ($isReported) {
            $user->postReports()->detach(['post_id' => $post->post_id]);
            $status = 'Report withdrawn';
        } else {
            $status = 'You haven\'t reported the post';
        }

        return ['message' => $status];
    }
}
