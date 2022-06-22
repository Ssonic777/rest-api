<?php

declare(strict_types=1);

namespace App\Services\ServiceHandlers;

use App\Exceptions\Contracts\ExceptionMessageInterface;
use App\Handlers\Blog\CommentReply\BlogCommentReplySetIsLikedAttributeHandler;
use App\Handlers\Comment\CommentSaveAttachmentHandler;
use App\Http\Resources\CommentReplyResource;
use App\Models\CommentReply;
use App\Models\User;
use App\Repositories\Redis\FileRepository;
use App\Services\Files\FileService;
use App\Traits\FileTrait;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * class BlogCommentReplyServiceHandler
 * @package App\Services\ServiceHandlers
 */
class BlogCommentReplyServiceHandler
{
    use FileTrait;

    /**
     * @param array $attributes
     * @return array|iterable
     */
    public function parseModelAttributes(array $attributes): array
    {
        foreach (CommentReplyResource::MODIFY_ATTRIBUTES as $newKey => $oldKey) {
            if (array_key_exists($newKey, $attributes)) {
                $attributes[$oldKey] = $attributes[$newKey];
                unset($attributes[$newKey]);
            }
        }

        return $attributes;
    }

    /**
     * @param string $ability
     * @param array $arguments
     */
    public function checkRight(string $ability, ...$arguments): void
    {
        if (Gate::denies($ability, $arguments)) {
            throw new BadRequestException(ExceptionMessageInterface::DONT_RIGHT_MSG);
        }
    }

    /**
     * @param CommentReply $commentReply
     * @param User $user
     * @return CommentReply
     */
    public function setAttributes(CommentReply $commentReply, User $user): CommentReply
    {
        $commentReply->setHidden(['user_id']);
        BlogCommentReplySetIsLikedAttributeHandler::execute($commentReply, $user->user_id);

        return $commentReply;
    }

    /**
     * @param CommentReply $blogCommentReply
     * @param FileRepository $fileRepository
     * @param array $data
     * @return void
     */
    public function fileUpdate(CommentReply $blogCommentReply, FileRepository $fileRepository, array &$data): void
    {
        CommentSaveAttachmentHandler::execute($fileRepository, $data);
        $this->fileDelete($blogCommentReply);
    }

    /**
     * @param CommentReply $blogCommentReply
     * @return void
     */
    public function fileDelete(CommentReply $blogCommentReply): void
    {
        if (!empty($blogCommentReply->c_file)) {
            $this->deleteFile(FileService::FILE_PATH, $blogCommentReply->getRawOriginal('c_file'));
        }
    }
}
