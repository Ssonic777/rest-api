<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\Follower
 *
 * @property int $id
 * @property int $following_id
 * @property int $follower_id
 * @property int $is_typing
 * @property int $active
 * @property int $notify
 * @property int $time
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Follower newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Follower newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Follower query()
 * @method static \Illuminate\Database\Eloquent\Builder|Follower whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Follower whereFollowerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Follower whereFollowingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Follower whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Follower whereIsTyping($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Follower whereNotify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Follower whereTime($value)
 * @mixin \Eloquent
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Follower whereDeletedAt($value)
 */
class Follower extends Pivot
{
    use HasFactory;

    public const STATUS_ACTIVE = '1';
    public const STATUS_NOT_ACTIVE = '0';

    public const TEXT_FOLLOW_STATUSES = [
       'Follow request sent',
       'Followed'
    ];

    protected $table = 'Wo_Followers';

    public $timestamps = true;

    protected $fillable = [
        'follower_id',
        'following_id',
    ];

    /**
     * @return BelongsTo
     */
    public function following(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'following_id');
    }

    /**
     * @return BelongsTo
     */
    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'follower_id');
    }
}
