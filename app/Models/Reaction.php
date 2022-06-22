<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Reaction
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $post_id
 * @property int|null $comment_id
 * @property int|null $replay_id
 * @property int|null $reaction
 * @property-read \App\Models\Comment $comment
 * @property-read \App\Models\Post $post
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Reaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Reaction whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reaction wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reaction whereReaction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reaction whereReplayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reaction whereUserId($value)
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Reaction whereDeletedAt($value)
 */
class Reaction extends Model
{
    use HasFactory;

    public const LIKE = 1;
    public const LOVE = 2;
    public const HAHA = 3;
    public const WOW = 4;
    public const SAD = 5;
    public const ANGRY = 6;

    public $timestamps = false;
    public $table = 'Wo_Reactions';

    /**
     * @return BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'id');
    }

    /**
     * @return BelongsTo
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'id');
    }

    /**
     * @return BelongsTo
     */
    public function reply(): BelongsTo
    {
        return $this->belongsTo(CommentReply::class, 'id', 'replay_id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
