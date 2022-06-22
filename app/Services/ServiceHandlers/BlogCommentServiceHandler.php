<?php

declare(strict_types=1);

namespace App\Services\ServiceHandlers;

use App\Handlers\Blog\Comment\BlogCommentSetIsLikedAttributeHandler;
use App\Handlers\CheckPermission;
use App\Handlers\Contracts\ModelAttributesInterface;
use App\Handlers\Contracts\ModelDeleteAttributesInterface;
use App\Handlers\ModelAttributes;
use App\Handlers\ModelDeleteAttributes;
use App\Models\Comment;

/**
 * class BlogCommentServiceHandler
 * @package App\Services\ServiceHandlers
 */
class BlogCommentServiceHandler
{
    /**
     * @var ModelAttributesInterface $modelAttributes
     */
    public ModelAttributesInterface $modelAttributes;

    /**
     * @var ModelDeleteAttributesInterface $modelDeleteAttributes
     */
    public ModelDeleteAttributesInterface $modelDeleteAttributes;

    /**
     * @var CheckPermission $checkPermission
     */
    public CheckPermission $checkPermission;

    public function __construct(
        ModelAttributes $modelAttributes,
        ModelDeleteAttributes $modelDeleteAttributes,
        CheckPermission $checkPermission
    ) {
        $this->modelAttributes = $modelAttributes;
        $this->modelDeleteAttributes = $modelDeleteAttributes;
        $this->checkPermission = $checkPermission;
    }

    /**
     * @param Comment $comment
     * @param int $userId
     * @param int|null $blogId
     * @return Comment
     */
    public function setAttributes(Comment $comment, int $userId, int $blogId = null): Comment
    {
        $hiddenAttributes = ['user_id'];
        BlogCommentSetIsLikedAttributeHandler::execute($comment, $userId);

        if ($comment->relationLoaded('replies')) {
            $comment->replies->setIsLikedAttributes($userId);
        }

        if ($blogId) {
            $comment->setAttribute('blog_id', $blogId);
            $hiddenAttributes[] = 'post_id';
        }

        $comment->setHidden($hiddenAttributes);

        return $comment;
    }
}
