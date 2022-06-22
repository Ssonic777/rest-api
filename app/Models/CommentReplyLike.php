<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * class CommentReplyLike
 * @package App\Models
 */
class CommentReplyLike extends Pivot
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function reply(): BelongsTo
    {
        return $this->belongsTo(CommentReply::class, 'reply_id', 'id');
    }
}
