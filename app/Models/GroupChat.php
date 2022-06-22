<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\GroupChat
 *
 * @property int $group_id
 * @property int $user_id
 * @property string $group_name
 * @property string $avatar
 * @property string $time
 * @property string|null $group_public_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GroupChatAdmin[] $admins
 * @property-read int|null $admins_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GroupChatUser[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChat query()
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChat whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChat whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChat whereGroupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChat whereGroupPublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChat whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GroupChat whereUserId($value)
 * @mixin \Eloquent
 * @property int|null $privacy_type
 * @property string|null $deleted_at
 * @property-read \App\Models\User|null $admin
 * @property-read \App\Collections\MessageCollection|\App\Models\Message[] $messages
 * @property-read int|null $messages_count
 * @method static Builder|GroupChat whereDeletedAt($value)
 * @method static Builder|GroupChat wherePrivacyType($value)
 */
class GroupChat extends Model
{
    use HasFactory;

    protected $table = 'Wo_GroupChat';
    protected $primaryKey = 'group_id';
    public $timestamps = false;
    public static $folderPrefix = 'upload/chat/avatar';

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        'user_id',
        'group_name',
        'avatar',
    ];

    protected $with = [
        'admin',
        'users'
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model): void {
            $model->time = time();
        });

        self::deleting(function ($chat) {
            $chat->admins()->each(function ($admin): void {
                $admin->delete();
            });

            $chat->users()->each(function ($user): void {
                $user->delete();
            });
        });
    }

    /**
     * @return HasMany
     */
    public function admins(): HasMany
    {
        return $this->hasMany(GroupChatAdmin::class, 'GroupChatID');
    }

    /**
     * @return HasOne
     */
    public function admin(): HasOne
    {
        return $this->hasOne(User::class, 'user_id', 'user_id');
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'Wo_GroupChatUsers', 'group_id', 'user_id');
    }

    /**
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'group_id', 'group_id');
    }
}
