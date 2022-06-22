<?php

declare(strict_types=1);

namespace App\Models;

use App\Collections\MessageCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Class Message
 *
 * @package App\Models
 * @property int $id
 * @property int $from_id
 * @property int $group_id
 * @property int $page_id
 * @property int $to_id
 * @property string|null $text
 * @property string $media
 * @property string $mediaFileName
 * @property string $mediaFileNames
 * @property string $time
 * @property string $seen
 * @property string $deleted_one
 * @property string $deleted_two
 * @property int $sent_push
 * @property string $notification_id
 * @property string $type_two
 * @property string|null $stickers
 * @property int $product_id
 * @property string $lat
 * @property string $lng
 * @property int|null $reply_msg
 * @property int|null $call_id
 * @property int|null $video_call_id
 * @property string|null $deleted_at
 * @property-read \App\Models\User $from
 * @property-read \App\Models\GroupChat $group
 * @property-read Message|null $replied
 * @property-read MessageCollection|Message[] $replies
 * @property-read int|null $replies_count
 * @property-read \App\Models\User $to
 * @method static MessageCollection|static[] all($columns = ['*'])
 * @method static MessageCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCallId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereDeletedOne($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereDeletedTwo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereMedia($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereMediaFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereMediaFileNames($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereNotificationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereReplyMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereSentPush($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereStickers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereTypeTwo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereVideoCallId($value)
 * @mixin \Eloquent
 */

/**
 * class Message
 * @package App\Models
 */
class Message extends Model
{
    use HasFactory;

    public const MESSAGE_MEDIA_PATH = 'upload/files';
    public const MESSAGE_NOT_SEEN = 0;

    protected $table = 'Wo_Messages';

    protected $fillable = [
        'from_id',
        'to_id',
        'group_id',
        'page_id',
        'text',
        'media',
        'mediaFileName',
        'mediaFileNames',
        'time',
        'seen',
        'deleted_one',
        'deleted_two',
        'sent_push',
        'notification_id',
        'type_two',
        'stickers',
        'product_id',
        'lat',
        'lng',
        'reply_msg',
        'call_id',
        'video_call_id',
    ];

    public $timestamps = false;

    protected $casts = [
        'to_id' => 'integer',
        'group_id' => 'integer'
    ];

    protected $with = [
        'from',
        'to'
    ];

    /**
     * Get Message from_id
     *
     * @return int
     */
    public function getToId(): int
    {
        return $this->to_id;
    }

    /**
     * Get Message to_id
     *
     * @return int
     */
    public function getFromId(): int
    {
        return $this->from_id;
    }

    /**
     * Set Custom collect for Message model
     *
     * @param array $models
     * @return Collection
     */
    public function newCollection(array $models = []): Collection
    {
        return new MessageCollection($models);
    }

    /**
     * @return BelongsTo
     */
    public function from(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_id', 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function to(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_id', 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(GroupChat::class, 'group_id');
    }

    /**
     * @return HasMany
     */
    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'reply_msg', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function replied(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reply_msg', 'id');
    }
}
