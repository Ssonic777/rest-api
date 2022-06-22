<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\Blog\Comment\BlogCommentLikedEvent;
use App\Models\Comment;
use App\Models\Reaction;
use App\Models\User;
use App\Repositories\BlogRepository;
use App\Repositories\CommentRepository;

/**
 * class BlockdeskCommentLikeService
 * @package App\Services
 */
class BlockdeskCommentLikeService
{
    private BlogRepository $blogRepository;

    /**
     * @var CommentRepository $commentRepository
     */
    private CommentRepository $commentRepository;

    /**
     * @param BlogRepository $blogRepository
     * @param CommentRepository $commentRepository
     */
    public function __construct(BlogRepository $blogRepository, CommentRepository $commentRepository)
    {
        $this->blogRepository = $blogRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * @param User $user
     * @param int $articleId
     * @param int $commentId
     * @return array
     */
    public function toggleLike(User $user, int $articleId, int $commentId): array
    {
        /** @var Comment $foundComment */
        $article = $this->blogRepository->getArticle($articleId);
        $foundComment = $this->commentRepository->findBlogComment($article->post->post_id, $commentId);

        if ($foundComment->reactions->contains($user->user_id)) {
            $foundComment->reactions()->detach($user->user_id);
            $message = 'Unliked';
        } else {
            $foundComment->reactions()->attach($user->user_id, [
                'post_id' => $article->post->post_id,
                'reaction' => Reaction::LOVE,
            ]);
            BlogCommentLikedEvent::dispatch($foundComment, $user->user_id);
            $message = 'Liked';
        }

        return [
            'message' => $message,
            'likes_count' => $foundComment->reactions()->count()
        ];
    }
}
