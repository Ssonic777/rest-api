<?php

namespace App\Services;

use App\Events\Post\Comment\PostCommentCreatedEvent;
use App\Events\Post\Comment\PostCommentLikeEvent;
use App\Handlers\Comment\CommentSaveAttachmentHandler;
use App\Models\Comment;
use App\Models\CommentReply;
use App\Models\Reaction;
use App\Models\User;
use App\Repositories\CommentRepository;
use App\Repositories\Redis\FileRepository;
use App\Services\Files\FileService;
use App\Traits\FileTrait;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CommentService
{
    use FileTrait;

    /**
     * @var CommentRepository
     */
    public CommentRepository $repository;
    private FileRepository $fileRepository;

    /** @var array|string[] $with */
    public array $with = [
        'user:user_id,username,first_name,last_name,avatar',
        'replies:id,user_id,comment_id,text,c_file,time',
        'replies.user:user_id,username,first_name,last_name,avatar'
    ];

    public function __construct(CommentRepository $repository, FileRepository $fileRepository)
    {
        $this->repository = $repository;
        $this->repository->setWith($this->with);
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param User $user
     * @param int $postId
     * @param string $sortColumn
     * @param string $sortDirection
     * @param int $perPage
     * @return CursorPaginator
     */
    public function getPostComments(User $user, int $postId, string $sortColumn = 'id', string $sortDirection = 'ASC', int $perPage = 15): CursorPaginator
    {
        /** @var CursorPaginator $res */
        $postComments = $this->repository->getCommentsByPostId($postId, $sortColumn, $sortDirection, $perPage);
        $postComments->each(function (Comment $comment) use ($user): void {
            $comment = $this->setUserAttributes($comment, $user->user_id);
            $comment->replies->each(function (CommentReply $commentReply) use ($user): CommentReply {
                return $this->setUserCommentReplyAttributes($commentReply, $user->user_id);
            });
        });

        return $postComments;
    }

    /**
     * @param User $user
     * @param int $postId
     * @param int $commentId
     * @return Comment
     */
    public function showComment(User $user, int $postId, int $commentId): Comment
    {
        /** @var Comment $showComment */
        $showComment = $this->repository->findCommentByPostId($postId, $commentId);

        $showComment->replies->each(function (CommentReply $commentReply) use ($user): CommentReply {
            return $this->setUserCommentReplyAttributes($commentReply, $user->user_id);
        });

        return $this->setUserAttributes($showComment, $user->user_id);
    }

    /**
     * @param User $user
     * @param array $data
     * @return Comment
     */
    public function storeComment(User $user, array $data): Comment
    {
        CommentSaveAttachmentHandler::execute($this->fileRepository, $data);

        /** @var Comment $createdComment */
        $createdComment = $user->comments()->create($data);
        /** @var Comment $foundComment */
        $foundComment = $this->repository->setWith($this->with)
                                    ->find($createdComment->id);

        $foundComment->offsetUnset('user_id');
        PostCommentCreatedEvent::dispatch($createdComment);

        return $this->setUserAttributes($foundComment, $user->user_id);
    }

    /**
     * @param User $user
     * @param int $postId
     * @param int $commentId
     * @param array $data
     * @return Comment
     */
    public function updateComment(User $user, int $postId, int $commentId, array $data): Comment
    {
        $foundComment = $this->repository->findCommentByPostId($postId, $commentId);

        if (Gate::denies('update', $foundComment)) {
            throw new BadRequestException('Not authorized for the action.');
        }

        CommentSaveAttachmentHandler::execute($this->fileRepository, $data);

        if (array_key_exists('file', $data) && !empty($foundComment['c_file'])) {
            $this->deleteFile(FileService::FILE_PATH, $foundComment->getRawOriginal('c_file'));
        }


        /** @var Comment $updatedComment */
        $updatedComment = $this->repository->update($commentId, $data);

        /** @var Comment $foundComment */
        $foundComment = $this->repository->setWith($this->with)
                                        ->find($updatedComment->id);

        $foundComment->replies->each(function (CommentReply $commentReply) use ($user): CommentReply {
            return $this->setUserCommentReplyAttributes($commentReply, $user->user_id);
        });

        return $this->setUserAttributes($foundComment, $user->user_id);
    }

    /**
     * @param int $postId
     * @param int $commentId
     */
    public function deleteComment(int $postId, int $commentId): void
    {
        $foundComment = $this->repository->findCommentByPostId($postId, $commentId);

        if (Gate::denies('delete', $foundComment)) {
            throw new BadRequestException('Not authorized for the action.');
        }

        if (!empty($foundComment['c_file'])) {
            $this->deleteFile(FileService::FILE_PATH, $foundComment->getRawOriginal('c_file'));
        }

        $foundComment->delete();
    }

    /**
     * @param Comment $comment
     * @param int $userId
     * @return Comment
     */
    public function setUserAttributes(Comment $comment, int $userId): Comment
    {
        $isLiked = $comment->reactions()->wherePivot('user_id', '=', $userId)->exists();
        $comment->setAttribute('is_liked', $isLiked);

        $comment->offsetUnset('user_id');

        return $comment;
    }

    /**
     * @param CommentReply $commentReply
     * @param int $userId
     * @return CommentReply
     */
    public function setUserCommentReplyAttributes(CommentReply $commentReply, int $userId): CommentReply
    {
        $isLiked = $commentReply->reactions()->wherePivot('user_id', '=', $userId)->exists();
        $commentReply->setAttribute('is_liked', $isLiked);
        $hasLikesCount = $commentReply->hasAppended('likes_count');

        if (!$hasLikesCount) {
            $commentReply->loadCount('reactions');
        }

        $commentReply->offsetUnset('user_id');

        return $commentReply;
    }

    /**
     * @param User $user
     * @param int $commentId
     * @return array
     */
    public function toggleCommentLike(User $user, int $commentId): array
    {
        /** @var Comment $comment */
        $comment = $this->repository->find($commentId);
        $doesntLiked = $comment->reactions()->wherePivot('user_id', $user->user_id)->doesntExist();

        if ($doesntLiked) {
            $user->commentLikes()->attach(['id' => $comment->id], [
                'post_id' => $comment->post_id,
                'reaction' => Reaction::LOVE,
            ]);
            PostCommentLikeEvent::dispatch($comment);
            $status = 'Liked';
        } else {
            $user->commentLikes()->detach(['id' => $comment->id]);
            $status = 'Unliked';
        }

        return [
            'message' => $status,
            'likes_count' => $comment->reactions()->count(),
        ];
    }

    /**
     * @param User $user
     * @param Comment $comment
     * @param string $text
     * @return string[]
     */
    public function reportComment(User $user, Comment $comment, string $text): array
    {
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
     * @param Comment $comment
     * @return string[]
     */
    public function withdrawCommentReport(User $user, Comment $comment): array
    {
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
