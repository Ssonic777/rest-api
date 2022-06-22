<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\BlogRepository;
use App\Repositories\CommentRepository;
use App\Models\User;
use App\Models\Comment;

class BlockdeskCommentReportService
{
    private BlogRepository $blogRepository;

    private CommentRepository $repository;

    /**
     * @param BlogRepository $blogRepository
     * @param CommentRepository $repository
     */
    public function __construct(BlogRepository $blogRepository, CommentRepository $repository)
    {
        $this->blogRepository = $blogRepository;
        $this->repository = $repository;
    }

    /**
     * @param User $user
     * @param int $articleId
     * @param int $commentId
     * @param string $text
     * @return string[]
     */
    public function reportBlogComment(User $user, int $articleId, int $commentId, string $text): array
    {
        $article = $this->blogRepository->getArticle($articleId);
        $comment = $this->repository->findBlogComment($article->post->post_id, $commentId);
        $notReported = $comment->reports()->wherePivot('user_id', $user->user_id)->doesntExist();

        if ($notReported) {
            $user->commentReports()->attach(['comment_id' => $comment->id], ['text' => $text]);
            $status = 'Reported';
        } else {
            $status = 'You have reported the comment already';
        }

        return ['message' => $status];
    }

    /**
     * @param User $user
     * @param int $articleId
     * @param int $commentId
     * @return string[]
     */
    public function withdrawBlogCommentReport(User $user, int $articleId, int $commentId): array
    {
        $article = $this->blogRepository->getArticle($articleId);
        $comment = $this->repository->findBlogComment($article->post->post_id, $commentId);
        $reported = $comment->reports()->wherePivot('user_id', $user->user_id)->exists();

        if ($reported) {
            $user->commentReports()->detach(['comment_id' => $comment->id]);
            $status = 'Report withdrawn';
        } else {
            $status = 'You haven\'t reported the comment';
        }

        return ['message' => $status];
    }
}
