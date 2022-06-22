<?php

declare(strict_types=1);

namespace App\Services;

use App\Collections\CommentCollection;
use App\Handlers\Comment\CommentSaveAttachmentHandler;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\ProjectClass\ProjectCursorPaginator;
use App\Repositories\BlogRepository;
use App\Repositories\CommentRepository;
use App\Repositories\PostRepository;
use App\Repositories\Redis\FileRepository;
use App\Traits\FileTrait;
use App\Services\Files\FileService;
use App\Services\ServiceHandlers\BlogCommentServiceHandler;

/**
 * class BlogCommentService
 * @package App\Services
 */
class BlogCommentService
{
    use FileTrait;

    /**
     * @var BlogCommentServiceHandler $handler
     */
    private BlogCommentServiceHandler $handler;
    private FileRepository $fileRepository;

    /**
     * @var CommentRepository $repository
     */
    private CommentRepository $repository;

    private BlogRepository $blogRepository;

    private PostRepository $postRepository;

    public function __construct(
        BlogCommentServiceHandler $handler,
        CommentRepository $repository,
        FileRepository $fileRepository,
        BlogRepository $blogRepository,
        PostRepository $postRepository
    ) {
        $this->handler = $handler;
        $this->repository = $repository;
        $this->fileRepository = $fileRepository;
        $this->blogRepository = $blogRepository;
        $this->postRepository = $postRepository;
    }

    /**
     * @param User $user
     * @param int $blogId
     * @param int $perPage
     * @return ProjectCursorPaginator
     */
    public function getComments(User $user, int $blogId, int $perPage = 15): ProjectCursorPaginator
    {
        /** @var Post $blog */
        $post = $this->postRepository->findBy('blog_id', (string) $blogId);
        /** @var ProjectCursorPaginator|CommentCollection $comments */
        $comments = $this->repository->getBlogComments($post->post_id, $perPage);
        $comments->setIsLikedAttributes($user->user_id);
        $comments->each(fn (Comment $comment) => $this->handler->setAttributes($comment, $user->user_id, $blogId));

        return $comments;
    }

    /**
     * @param User $authUser
     * @param array $data
     * @return Comment
     */
    public function createBlogComment(User $authUser, array $data): Comment
    {
        $post = $this->postRepository->findBy('blog_id', (string) $data['article_id'], ['post_id', 'blog_id']);
        CommentSaveAttachmentHandler::execute($this->fileRepository, $data);

        /** @var Comment $createdBlogComment */
        $data = array_merge($data, ['user_id' => $authUser->user_id]);
        $createdBlogComment = $post->comments()->create($data);
        $createdBlogComment->loadMissing($this->repository::DEFAULT_WITH);
        $createdBlogComment->loadCount($this->repository::DEFAULT_WITH_COUNT);
        $this->handler->setAttributes($createdBlogComment, $authUser->user_id, $data['article_id']);

        return $createdBlogComment;
    }

    /**
     * @param User $user
     * @param int $blogId
     * @param int $commentId
     * @return Comment
     */
    public function showBlogComment(User $user, int $blogId, int $commentId): Comment
    {
        /** @var Post $post */
        $post = $this->postRepository->findBy('blog_id', (string) $blogId, ['post_id', 'blog_id']);
        $showBlogComment = $this->repository->findBlogComment($post->post_id, $commentId);

        $showBlogComment->replies->each->setHidden(['user_id', 'page_id', 'edited']);
        $showBlogComment->loadMissing($this->repository::DEFAULT_WITH);

        return $this->handler->setAttributes($showBlogComment, $user->user_id, $blogId);
    }

    /**
     * @param User $authUser
     * @param array $validated
     * @return Comment
     */
    public function updateBlogComment(User $authUser, array $validated): Comment
    {
        /** @var Post $post */
        $post = $this->postRepository->findBy('blog_id', (string) $validated['article_id'], ['post_id', 'blog_id']);
        $findBlogComment = $this->repository->findBlogComment($post->post_id, $validated['comment_id']);
        $this->handler->checkPermission->execute('update', $findBlogComment);

        $updateFields = [];
        if (array_key_exists('text', $validated)) {
            $updateFields['text'] = $validated['text'];
        }

        if (array_key_exists('file', $validated)) {
            CommentSaveAttachmentHandler::execute($this->fileRepository, $validated);
            $updateFields['c_file'] = $validated['c_file'];

            if (!empty($findBlogComment['c_file'])) {
                $this->deleteFile(FileService::FILE_PATH, $findBlogComment->getRawOriginal('c_file'));
            }
        }

        $findBlogComment->update($updateFields);
        $this->handler->setAttributes($findBlogComment, $authUser->user_id, $validated['article_id']);

        return $findBlogComment;
    }

    /**
     * @param int $blogId
     * @param int $commentId
     */
    public function deleteBlogComment(int $blogId, int $commentId): void
    {
        /** @var Post $post */
        $post = $this->postRepository->findBy('blog_id', (string) $blogId, ['post_id', 'blog_id']);
        $findBlogComment = $this->repository->findBlogComment($post->post_id, $commentId);
        $this->handler->checkPermission->execute('delete', $findBlogComment);
        if (!empty($findBlogComment['c_file'])) {
            $this->deleteFile(FileService::FILE_PATH, $findBlogComment->getRawOriginal('c_file'));
        }
        $findBlogComment->delete();
    }
}
