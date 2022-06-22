<?php

declare(strict_types=1);

namespace App\Services;

use App\Collections\CommentReplyCollection;
use App\Events\Blog\CommentReply\BlogCommentReplyCreatedEvent;
use App\Handlers\Comment\CommentSaveAttachmentHandler;
use App\Models\BlogCommentReply;
use App\Models\CommentReply;
use App\Models\User;
use App\ProjectClass\ProjectCursorPaginator;
use App\Repositories\CommentReplyRepository;
use App\Repositories\Redis\FileRepository;
use App\Services\ServiceHandlers\BlogCommentReplyServiceHandler;

/**
 * class BlogCommentReplyService
 * @package App\Services
 */
class BlogCommentReplyService
{
    /**
     * @var CommentReplyRepository $repository
     */
    private CommentReplyRepository $repository;

    /**
     * @var BlogCommentReplyServiceHandler $handler
     */
    private BlogCommentReplyServiceHandler $handler;

    /**
     * @var FileRepository $fileRepository
     */
    private FileRepository $fileRepository;

    public function __construct(
        CommentReplyRepository $repository,
        BlogCommentReplyServiceHandler $handler,
        FileRepository $fileRepository
    ) {
        $this->repository = $repository;
        $this->handler = $handler;
        $this->fileRepository = $fileRepository;
    }

    /**
     * @param User $user
     * @param int $blogId
     * @param int $commentId
     * @param int|null $perPage
     * @return ProjectCursorPaginator
     */
    public function getCommentReplies(User $user, int $blogId, int $commentId, int $perPage = null): ProjectCursorPaginator
    {
        /** @var ProjectCursorPaginator|CommentReplyCollection $commentReplies */
        $commentReplies = $this->repository->getBlogCommentReplies($commentId, $perPage);
        $commentReplies->each(fn (CommentReply $commentReply): CommentReply => $this->handler->setAttributes($commentReply, $user));

        return $commentReplies;
    }

    /**
     * @param User $user
     * @param array $data
     * @return CommentReply
     */
    public function storeCommentReply(User $user, array $data): CommentReply
    {
        CommentSaveAttachmentHandler::execute($this->fileRepository, $data);
        unset($data['file']);

        /** @var BlogCommentReply $createdCommentReply */
        $data = array_merge($this->handler->parseModelAttributes($data), ['user_id' => $user->user_id]);
        /** @var CommentReply $createdCommentReply */
        $createdCommentReply = $this->repository->create($data);
        $createdCommentReply->loadMissing($this->repository::DEFAULT_WITH);
        $createdCommentReply->loadCount($this->repository::DEFAULT_WITH_COUNT);
        BlogCommentReplyCreatedEvent::dispatch($createdCommentReply);
        $this->handler->setAttributes($createdCommentReply, $user);

        return $createdCommentReply;
    }

    /**
     * @param User $user
     * @param int $blogId
     * @param int $commentId
     * @param int $commentReplyId
     * @return CommentReply
     */
    public function showCommentReply(User $user, int $blogId, int $commentId, int $commentReplyId): CommentReply
    {
        $foundCommentReply = $this->repository->findCommentReply($commentId, $commentReplyId);
        $this->handler->setAttributes($foundCommentReply, $user);

        return $foundCommentReply;
    }

    /**
     * @param User $user
     * @param array $data
     * @return CommentReply
     */
    public function updateCommentReply(User $user, array $data): CommentReply
    {
        ['comment_id' => $commentId, 'comment_reply_id' => $commentReplyId] = $data;

        $foundCommentReply = $this->repository->findCommentReply($commentId, $commentReplyId);
        $this->handler->checkRight('update', $foundCommentReply);
        $this->handler->fileUpdate($foundCommentReply, $this->fileRepository, $data);
        $foundCommentReply->update($data);

        return $this->handler->setAttributes($foundCommentReply, $user);
    }

    /**
     * @param int $blogId
     * @param int $commentId
     * @param int $commentReplyId
     */
    public function deleteCommentReply(int $blogId, int $commentId, int $commentReplyId): void
    {
        $foundCommentReply = $this->repository->findCommentReply($commentId, $commentReplyId);
        $this->handler->checkRight('delete', $foundCommentReply);
        $this->handler->fileDelete($foundCommentReply);
        $foundCommentReply->delete();
    }
}
