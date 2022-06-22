<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Report
 *
 * @package App\Models
 * @property int $id
 * @property int $post_id
 * @property int $comment_id
 * @property int $profile_id
 * @property int $page_id
 * @property int $group_id
 * @property int $user_id
 * @property int $gchat_id
 * @property string|null $text
 * @property int $seen
 * @property int $time
 * @property string|null $deleted_at
 * @property-read \App\Models\Post $post
 * @method static \Illuminate\Database\Eloquent\Builder|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereGchatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereUserId($value)
 * @mixin \Eloquent
 */

/**
 * class Report
 * @package App\Models
 */
class Report extends Model
{
    use HasFactory;

    public const REPORT_TYPES = [
        'post',
        'comment',
        'user',
        'gchat',
    ];

    protected $table = 'Wo_Reports';

    protected $fillable = [
        'post_id',
        'comment_id',
        'profile_id',
        'page_id',
        'group_id',
        'user_id',
        'gchat_id',
        'text',
        'seen',
        'time',
    ];

    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id', 'post_id');
    }
}
