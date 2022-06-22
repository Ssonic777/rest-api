<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\CommentReplyLike;
use App\Repositories\Base\BaseModelRepository;

/**
 * class CommentReplyLikeRepository
 * @package App\Repositories
 */
class CommentReplyLikeRepository extends BaseModelRepository
{
    protected function getModel(): string
    {
        return CommentReplyLike::class;
    }
}
