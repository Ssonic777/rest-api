<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Model\Filterable;
use App\Collections\GroupCollection;
use App\Traits\Model\DBDefault;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * class Group
 * @package App\Models
 *
 * @property int $id
 * @property int $user_id
 * @property string $group_name
 * @property string $group_title
 * @property string $avatar
 * @property string $cover
 * @property string $about
 * @property int $category
 * @property string $sub_category
 * @property bool $privacy
 * @property string $join_privacy
 * @property string $active
 * @property string $registered
 * @property string|null $deleted_at
 * @property-read \App\Models\GroupCategory $catry
 * @method static \Illuminate\Database\Eloquent\Builder|Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Group query()
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereAbout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereGroupName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereGroupTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereJoinPrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group wherePrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereRegistered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereSubCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Group whereUserId($value)
 * @mixin \Eloquent
 */

/**
 * class Group
 * @package App\Models
 */
class Group extends Model
{
    use HasFactory;
    use Filterable;
    use DBDefault;

    // use SoftDeletes;

    public const PRIVACY = '2';
    public const NOT_PRIVACY = '1';

    public const JOIN_PRIVACY = '2';
    public const NOT_JOIN_PRIVACY = '1';

    public const PRIVACIES = [
        self::PRIVACY,
        self::NOT_PRIVACY,
    ];

    public const JOIN_PRIVACIES = [
        self::JOIN_PRIVACY,
        self::NOT_JOIN_PRIVACY
    ];

    public const MEDIA_MIMETYPES = [
        'image/jpeg',
        'image/jpg',
        'image/png'
    ];

    public const DEFAULT_AVATAR = 'upload/photos/d-group.jpg';
    public const DEFAULT_COVER = 'upload/photos/d-cover.jpg';

    public const DEFAULT_MEDIAS = [
        self::DEFAULT_AVATAR,
        self::DEFAULT_COVER
    ];

    public const GROUP_COVER = 'group/cover';
    public const GROUP_AVATAR = 'group/avatar';

    public const GROUP_ACTIVE = '1';
    public const GROUP_NOT_ACTIVE = '0';

    protected $table = 'Wo_Groups';

    protected $fillable = [
        'user_id',
        'group_name',
        'group_title',
        'avatar',
        'cover',
        'about',
        'category',
        'sub_category',
        'privacy',
        'join_privacy',
        'active',
        'registered',
        'deleted_at'
    ];

    public $timestamps = false;

    /**
     * @var int $perPage
     */
    public $perPage = 15;

    /**
     * @var string[] $hidden
     */
    protected $hidden = [
        'sub_category'
    ];

    protected $casts = [
        'active' => 'boolean',
        'join_privacy' => 'boolean'
    ];

    /**
     * @return bool
     */
    public function getPrivacyAttribute(): bool
    {
        return $this->isPrivacy();
    }

    /**
     * @return bool
     */
    public function getJoinPrivacyAttribute(): bool
    {
        return $this->isJoinPrivacy();
    }

    /**
     * @return bool
     */
    public function isJoinPrivacy(): bool
    {
        return $this->attributes['join_privacy'] == self::JOIN_PRIVACY;
    }

    /**
     * @return bool
     */
    public function isPrivacy(): bool
    {
        return $this->attributes['privacy'] == self::PRIVACY;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isOwner(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * @return HasOne
     */
    public function notification(): HasOne
    {
        return $this->hasOne(Notification::class, 'id', 'group_id');
    }

    /**
     * @param string $value
     * @return string
     */
    public function getAvatarAttribute(string $value): ?string
    {
        return $this->checkDBDefaultValue(self::DEFAULT_AVATAR, 'avatar');
    }

    /**
     * @param string $value
     * @return string
     */
    public function getCoverAttribute(string $value): ?string
    {
        return $this->checkDBDefaultValue(self::DEFAULT_COVER, 'cover');
    }

    /**
     * @return BelongsTo
     */
    public function catry(): BelongsTo
    {
        return $this->belongsTo(GroupCategory::class, 'category', 'id');
    }

    /**
     * @return HasOne
     */
    public function setting(): HasOne
    {
        return $this->hasOne(GroupAdditionalData::class, 'group_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'Wo_Group_Members', 'group_id', 'user_id')->using(GroupMember::class);
    }

    /**
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'post_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'Wo_GroupAdmins', 'group_id', 'user_id')->using(GroupAdmin::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
