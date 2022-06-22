<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\Blog\CommentReply\BlogCommentReplyLikedEvent;
use App\Models\CommentReply;
use App\Models\Reaction;
use App\Models\User;
use App\Repositories\CommentReplyRepository;

/**
 * class CommentReplyLikeService
 * @package App\Services
 */
class CommentReplyLikeService
{
    /**
     * @var CommentReplyRepository $commentReplyRepository
     */
    private CommentReplyRepository $commentReplyRepository;

    public function __construct(CommentReplyRepository $commentReplyRepository)
    {
        $this->commentReplyRepository = $commentReplyRepository;
    }

    /**
     * @param User $user
     * @param int $replyCommentId
     * @return array
     */
    public function toggleLike(User $user, int $replyCommentId): array
    {
        /** @var CommentReply $foundReplyComment */
        $foundReplyComment = $this->commentReplyRepository->find($replyCommentId);

        if ($foundReplyComment->reactions->contains($user->user_id)) {
            $foundReplyComment->reactions()->detach($user->user_id);
            $message = 'Unliked';
        } else {
            $foundReplyComment->reactions()->attach($user->user_id, ['reaction' => Reaction::LOVE]);
            BlogCommentReplyLikedEvent::dispatch($foundReplyComment, $user->user_id);
            $message = 'Liked';
        }

        return [
            'message' => $message,
            'likes_count' => $foundReplyComment->reactions()->count(),
        ];
    }
}
