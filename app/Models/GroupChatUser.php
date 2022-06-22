<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\GroupChatUser
 *
 * @property int $id
 * @property int $user_id
 * @property int $group_id
 * @property string $active
 * @property string $last_seen
 * @property-read GroupChat $chat
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChatUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChatUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChatUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChatUser whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChatUser whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChatUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChatUser whereLastSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChatUser whereUserId($value)
 * @mixin \Eloquent
 * @property string|null $deleted_at
 * @property-read \App\Models\GroupChat|null $group
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChatUser whereDeletedAt($value)
 */
class GroupChatUser extends Model
{
    use HasFactory;

    protected $table = 'Wo_GroupChatUsers';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'group_id',
        'active',
    ];

    protected $with = [
        'group'
    ];

    /**
     * @return BelongsTo
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(GroupChat::class);
    }

    public function group(): HasOne
    {
        return $this->hasOne(GroupChat::class, 'group_id', 'group_id');
    }
}
