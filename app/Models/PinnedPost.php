<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PinnedPost
 *
 * @property int $id
 * @property int $user_id
 * @property int $page_id
 * @property int $group_id
 * @property int $post_id
 * @property int $event_id
 * @property string $active
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PinnedPost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PinnedPost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PinnedPost query()
 * @method static \Illuminate\Database\Eloquent\Builder|PinnedPost whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PinnedPost whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PinnedPost whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PinnedPost whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PinnedPost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PinnedPost wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PinnedPost wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PinnedPost whereUserId($value)
 */
class PinnedPost extends Model
{
    use HasFactory;

    protected $table = 'Wo_PinnedPosts';

    public $timestamps = false;

    public const STATUS_ACTIVE = '1';
    public const STATUS_NOT_ACTIVE = '0';

    public $fillable = [
        'post_id',
        'user_id',
        'active',
    ];
}
