<?php

declare(strict_types=1);

namespace App\Models;

use App\Collections\PostCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Post
 *
 * @property int $id
 * @property int $post_id
 * @property int $user_id
 * @property int $recipient_id
 * @property string|null $postText
 * @property int $page_id
 * @property int $group_id
 * @property int $event_id
 * @property int $page_event_id
 * @property string $postLink
 * @property string|null $postLinkTitle
 * @property string $postLinkImage
 * @property string $postLinkContent
 * @property string $postVimeo
 * @property string $postDailymotion
 * @property string $postFacebook
 * @property string $postFile
 * @property string $postFileName
 * @property string $postFileThumb
 * @property string $postYoutube
 * @property string $postVine
 * @property string $postSoundCloud
 * @property string $postPlaytube
 * @property string $postDeepsound
 * @property string $postMap
 * @property int $postShare
 * @property string $postPrivacy
 * @property string $postType
 * @property string $postFeeling
 * @property string $postListening
 * @property string $postTraveling
 * @property string $postWatching
 * @property string $postPlaying
 * @property string $postPhoto
 * @property string $time
 * @property string $registered
 * @property string $album_name
 * @property string $multi_image
 * @property int $multi_image_post
 * @property int $boosted
 * @property int $product_id
 * @property int $poll_id
 * @property int $blog_id
 * @property int $forum_id
 * @property int $thread_id
 * @property int $videoViews
 * @property string $postRecord
 * @property string|null $postSticker
 * @property int $shared_from
 * @property string|null $post_url
 * @property int $parent_id
 * @property int $cache
 * @property int $comments_status
 * @property int $blur
 * @property int $color_id
 * @property int $job_id
 * @property int $offer_id
 * @property int $fund_raise_id
 * @property int $fund_id
 * @property int $active
 * @property string $stream_name
 * @property int $live_time
 * @property int $live_ended
 * @property string|null $agora_resource_id
 * @property string $agora_sid
 * @property string $send_notify
 * @property int|null $enable_notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Like[] $likes
 * @property-read int|null $likes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reaction[] $reactions
 * @property-read int|null $reactions_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereAgoraResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereAgoraSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereAlbumName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereBlogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereBlur($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereBoosted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCache($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereColorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCommentsStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereEnableNotifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereForumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereFundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereFundRaiseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereJobId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereLiveEnded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereLiveTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereMultiImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereMultiImagePost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereOfferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePageEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePollId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostDailymotion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostDeepsound($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostFeeling($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostFileThumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostLinkContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostLinkImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostLinkTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostListening($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostMap($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostPlaying($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostPlaytube($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostPrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostRecord($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostShare($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostSoundCloud($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostSticker($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostTraveling($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostVimeo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostVine($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostWatching($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePostYoutube($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereRecipientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereRegistered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereSendNotify($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereSharedFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereStreamName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereThreadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereVideoViews($value)
 * @mixin \Eloquent
 * @property string|null $video_name
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $likedUsers
 * @property-read int|null $liked_users_count
 * @property-read Post|null $sharedFrom
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereVideoName($value)
 */
class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const POST_STATUS_INACTIVE = 0;
    public const POST_STATUS_ACTIVE = 1;

    public const POST_IMAGE_PATH = 'upload/photos';

    public const PRIVACY_EVERYONE = 0;
    public const PRIVACY_PEOPLE_I_FOLLOW = 1;
    public const PRIVACY_PEOPLE_FOLLOW_ME = 2;
    public const PRIVACY_ONLY_ME = 3;
    public const ENABLE_NOTIFICATION = 1;
    public const DISABLE_NOTIFICATION = 0;

    public static array $privacyOptions = [
        Post::PRIVACY_EVERYONE,
        Post::PRIVACY_PEOPLE_I_FOLLOW,
        Post::PRIVACY_PEOPLE_FOLLOW_ME,
        Post::PRIVACY_ONLY_ME,
    ];

    public const ATTACHMENT_MIMETYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        // 'application/zip',
    ];

    public $timestamps = false;

    protected $table = 'Wo_Posts';

    public $perPage = 15;

    public $fillable = [
        'id',
        'user_id',
        'recipient_id',
        'postText',
        'page_id',
        'group_id',
        'event_id',
        'page_event_id',
        'postLink',
        'active',
        'postPrivacy',
        'time',
        'postFile',
        'postFileName',
        'postPhoto',
        'postType',
        'shared_from',
        'comments_status',
        'enable_notifications',
        'postSticker',
        'parent_id',
        'post_url',
        'multi_image',
    ];

    public static $feedFields = [
        'id',
        'user_id',
        'recipient_id',
        'postText',
        'page_id',
        'group_id',
        'event_id',
        'page_event_id',
        'postLink',
        'active',
        'postPrivacy',
        'time',
        'postFile'
    ];

    /**
     * @param array $models
     * @return PostCollection
     */
    public function newCollection(array $models = []): PostCollection
    {
        return new PostCollection($models);
    }

    /**
     * @return array
     */
    public function getPostPhotosAttribute(): array
    {
        return array_map(
            fn ($el): string => sprintf("%s/{$el}", getenv('AWS_CDN')),
            array_column($this->medias->toArray(), 'image')
        );
    }

    /**
     * @param string $value
     */
    public function setPostVideoAttribute(string $value): void
    {
        $this->attributes['video_name'] = $value;
    }

    /**
     * @var string[] $hidden
     */
    protected $hidden = [
        'id',
        'user_id',
        'parent_id',
    ];

    protected $casts = [
        'active' => 'boolean',
        'pin_count' => 'boolean',
        'comments_status' => 'boolean',
        'postPrivacy' => 'integer',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsToMany
     */
    public function reactions(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'Wo_Reactions', 'post_id', 'user_id', 'post_id', 'user_id');
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'post_id', 'post_id');
    }

    /**
     * @param $query
     */
    public static function withCommentsTree($query): void
    {
        $query->select(['id', 'user_id', 'post_id', 'text', 'c_file', 'time'])
            ->with(
                [
                    'user' => function ($query) {
                        $query->select(User::$publicFields);
                    },
                    'replies' => function ($query) {
                        $query->select(['id', 'comment_id', 'user_id', 'text', 'c_file', 'time'])
                            ->with(
                                [
                                    'user' => function ($query) {
                                        $query->select(User::$publicFields);
                                    }
                                ]
                            );
                    }
                ]
            );
    }

    /**
     * @param $query
     */
    public static function withReactions($query): void
    {
        $query->select()
            ->with(
                [
                    'user' => function ($query) {
                        $query->select(User::$publicFields);
                    }
                ]
            );
    }

    /**
     * @param BelongsTo $query
     */
    public static function withFeedAuthor(BelongsTo $query): void
    {
        $query->select(User::$publicFields);
    }

    /**
     * @return BelongsToMany
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'Wo_Likes', 'post_id', 'user_id', 'post_id', 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function medias(): HasMany
    {
        return $this->hasMany(AlbumMedia::class, 'post_id', 'post_id');
    }

    /**
     * @return HasOne
     */
    public function pin(): HasOne
    {
        return $this->hasOne(PinnedPost::class, 'post_id', 'post_id');
    }

    /**
     * @return HasOne
     */
    public function parent(): HasOne
    {
        return $this->hasOne(self::class, 'post_id', 'parent_id');
    }

    /**
     * @return BelongsToMany
     */
    public function hashtags(): BelongsToMany
    {
        return $this->belongsToMany(Hashtag::class, 'wo_hashtag_wo_post', 'wo_post_id', 'wo_hashtag_id', 'post_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class, 'id', 'blog_id');
    }

    /**
     * @return BelongsToMany
     */
    public function reports(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'Wo_Reports', 'post_id', 'user_id', 'post_id', 'user_id');
    }
}
