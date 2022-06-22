<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\PostCommentReplyCreatedEvent;
use App\Events\Post\CommentReply\PostCommentReplyLikeEvent;
use App\Handlers\Comment\CommentSaveAttachmentHandler;
use App\Models\CommentReply;
use App\Models\Reaction;
use App\Models\User;
use App\Repositories\CommentReplyRepository;
use App\Repositories\Redis\FileRepository;
use App\Services\Files\FileService;
use App\Traits\FileTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\CursorPaginator;

/**
 * Class CommentReplyService
 * @package App\Services
 */
class CommentReplyService
{
    use FileTrait;

    /**
    * @var CommentReplyRepository $repository
    */
    public CommentReplyRepository $repository;
    private FileRepository $fileRepository;

    public function __construct(CommentReplyRepository $repository, FileRepository $fileRepository)
    {
        $this->repository = $repository;
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param int $commentId
     * @return CursorPaginator
     */
    public function getCommentReplies(int $commentId): CursorPaginator
    {
        $replies = $this->repository->getRepliesByCommentId($commentId);

        $replies->each(function (CommentReply $reply) {
            $this->prepareResponse($reply);
        });

        return $replies;
    }

    /**
     * @param int $commentId
     * @param int $replyId
     * @return CommentReply
     */
    public function showCommentReply(int $commentId, int $replyId): CommentReply
    {
        $reply = $this->repository->findCommentReply($commentId, $replyId);

        return $this->prepareResponse($reply);
    }

    /**
     * @param User $user
     * @param array $data
     * @return CommentReply
     */
    public function storeCommentReply(User $user, array $data): CommentReply
    {
        CommentSaveAttachmentHandler::execute($this->fileRepository, $data);

        /** @var CommentReply $reply */
        $reply = $user->commentReplies()->create($data);
        $reply = $this->repository->findCommentReply($reply->comment_id, $reply->id);
        PostCommentReplyCreatedEvent::dispatch($reply);

        return $this->prepareResponse($reply);
    }

    /**
     * @param int $commentId
     * @param int $replyId
     * @param array $data
     * @return CommentReply
     */
    public function updateCommentReply(int $commentId, int $replyId, array $data): CommentReply
    {
        $reply = $this->repository->findCommentReply($commentId, $replyId);

        CommentSaveAttachmentHandler::execute($this->fileRepository, $data);

        if (array_key_exists('file', $data) && !empty($reply['c_file'])) {
            $this->deleteFile(FileService::FILE_PATH, $reply->getRawOriginal('c_file'));
        }

        $reply->update($data);
        $reply = $this->repository->findCommentReply($commentId, $replyId);

        return $this->prepareResponse($reply);
    }

    /**
     * @param int $commentId
     * @param int $replyId
     */
    public function deleteCommentReply(int $commentId, int $replyId): void
    {
        $reply = $this->repository->findCommentReply($commentId, $replyId);

        $this->deleteFile(CommentReply::COMMENT_IMAGE_PATH, $reply->c_file);
        $reply->delete();
    }

    /**
     * @param User $user
     * @param int $replyId
     * @return array
     */
    public function toggleCommentReplyLike(User $user, int $replyId): array
    {
        /** @var CommentReply $reply */
        $reply = $this->repository->find($replyId);
        $doesntLiked = $reply->reactions()->wherePivot('user_id', $user->user_id)->doesntExist();

        if ($doesntLiked) {
            $user->commentRepliesLikes()->attach(['id' => $reply->id], ['reaction' => Reaction::LOVE]);
            PostCommentReplyLikeEvent::dispatch($reply);
            $status = 'Liked';
        } else {
            $user->commentRepliesLikes()->detach(['id' => $reply->id]);
            $status = 'Unliked';
        }

        return [
            'message' => $status,
            'likes_count' => $reply->reactions()->count(),
        ];
    }

    /**
     * @param CommentReply $reply
     * @return CommentReply
     */
    public function prepareResponse(CommentReply $reply): CommentReply
    {
        $reply->offsetUnset('user_id');

        return $reply;
    }
}
