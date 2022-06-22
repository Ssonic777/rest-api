<?php

declare(strict_types=1);

namespace App\Models;

use App\Collections\UserCollection;
use App\Models\BaseModels\JWTAuthorizeModel;
use App\Search\UserSearch;
use App\Traits\Model\DBDefault;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\User
 *
 * @property int $user_id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property string $avatar
 * @property string $cover
 * @property string $background_image
 * @property string $background_image_status
 * @property int $relationship_id
 * @property string $address
 * @property string $working
 * @property string $working_link
 * @property string|null $about
 * @property string $school
 * @property string $gender
 * @property string $birthday
 * @property int $country_id
 * @property string $website
 * @property string $facebook
 * @property string $google
 * @property string $twitter
 * @property string $linkedin
 * @property string $youtube
 * @property string $vk
 * @property string $instagram
 * @property string $language
 * @property string $email_code
 * @property string $src
 * @property string|null $ip_address
 * @property string $follow_privacy
 * @property string $friend_privacy
 * @property string $post_privacy
 * @property string $message_privacy
 * @property string $confirm_followers
 * @property string $show_activities_privacy
 * @property string $birth_privacy
 * @property string $visit_privacy
 * @property string $verified
 * @property string $lastseen
 * @property string $showlastseen
 * @property string $emailNotification
 * @property string $e_liked
 * @property string $e_wondered
 * @property string $e_shared
 * @property string $e_followed
 * @property string $e_commented
 * @property string $e_visited
 * @property string $e_liked_page
 * @property string $e_mentioned
 * @property string $e_joined_group
 * @property string $e_accepted
 * @property string $e_profile_wall_post
 * @property string $e_sentme_msg
 * @property string $e_last_notif
 * @property string $notification_settings
 * @property string $status
 * @property string $active
 * @property string $admin
 * @property string $type
 * @property string $registered
 * @property string $start_up
 * @property string $start_up_info
 * @property string $startup_follow
 * @property string $startup_image
 * @property int $last_email_sent
 * @property string $phone_number
 * @property int $sms_code
 * @property string $is_pro
 * @property int $pro_time
 * @property string $pro_type
 * @property int $joined
 * @property string $css_file
 * @property string $timezone
 * @property int $referrer
 * @property int $ref_user_id
 * @property string $balance
 * @property string $paypal_email
 * @property string $notifications_sound
 * @property string $order_posts_by
 * @property string $social_login
 * @property string $android_m_device_id
 * @property string $ios_m_device_id
 * @property string $android_n_device_id
 * @property string $ios_n_device_id
 * @property string $web_device_id
 * @property string $wallet
 * @property string $lat
 * @property string $lng
 * @property string $last_location_update
 * @property int $share_my_location
 * @property int $last_data_update
 * @property string $details
 * @property string|null $sidebar_data
 * @property int $last_avatar_mod
 * @property int $last_cover_mod
 * @property float $points
 * @property int $daily_points
 * @property string $point_day_expire
 * @property int $last_follow_id
 * @property int $share_my_data
 * @property string|null $last_login_data
 * @property int $two_factor
 * @property string $new_email
 * @property int $two_factor_verified
 * @property string $new_phone
 * @property string $info_file
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property int $school_completed
 * @property string $weather_unit
 * @property string $paystack_ref
 * @property-read Collection|Follower[] $followers
 * @property-read int|null $followers_count
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|Post[] $posts
 * @property-read int|null $posts_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAbout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAndroidMDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAndroidNDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBackgroundImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBackgroundImageStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBirthPrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereConfirmFollowers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCssFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDailyPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEAccepted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereECommented($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEFollowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEJoinedGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereELastNotif($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereELiked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereELikedPage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEMentioned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEProfileWallPost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereESentmeMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEShared($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEVisited($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEWondered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailNotification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFollowPrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFriendPrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGoogle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereInfoFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIosMDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIosNDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsPro($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereJoined($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastAvatarMod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastCoverMod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastDataUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastEmailSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastFollowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLocationUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLoginData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastseen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLinkedin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMessagePrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNewEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNewPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotificationSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotificationsSound($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOrderPostsBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePaypalEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePaystackRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePointDayExpire($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePostPrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRefUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereReferrer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRegistered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRelationshipId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSchool($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSchoolCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereShareMyData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereShareMyLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereShowActivitiesPrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereShowlastseen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSidebarData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSmsCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSocialLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSrc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStartUp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStartUpInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStartupFollow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStartupImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereVisitPrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereVk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereWallet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereWeatherUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereWebDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereWorking($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereWorkingLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereYoutube($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereZip($value)
 * @property string $group_chat_privacy
 * @property int|null $waitlist
 * @property string|null $kickofflabs_json
 * @property int|null $is_public
 * @property string|null $deleted_at
 * @property-read Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read Collection|User[] $followings
 * @property-read int|null $followings_count
 * @property-read \App\Collections\MessageCollection|\App\Models\Message[] $fromMessages
 * @property-read int|null $from_messages_count
 * @property-read Collection|\App\Models\GroupChat[] $groupChats
 * @property-read int|null $group_chats_count
 * @property-read int|null $groups_count
 * @property-read Collection|\App\Models\GroupChat[] $myGroupChats
 * @property-read int|null $my_group_chats_count
 * @property-read Collection|\App\Models\Post[] $postLikes
 * @property-read int|null $post_likes_count
 * @property-read \App\Collections\MessageCollection|\App\Models\Message[] $toMessages
 * @property-read int|null $to_messages_count
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGroupChatPrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereKickofflabsJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereWaitlist($value)
 * @property-read Collection|\App\Models\CommentReply[] $commentReplies
 * @property-read int|null $comment_replies_count
 * @property-read \App\Collections\PostCollection|\App\Models\Post[] $commentRepliesLikes
 * @property-read int|null $comment_replies_likes_count
 * @property-read \App\Models\Country $country
 * @property-read \App\Models\UserField|null $field
 * @property-read int $phone_number_code
 * @property-read Collection|\App\Models\Group[] $groups
 * @property-read Collection|\App\Models\Group[] $myGroups
 * @property-read int|null $my_groups_count
 * @property-read \App\Collections\PostCollection|\App\Models\Post[] $postReactions
 * @property-read int|null $post_reactions_count
 * @property-read Collection|\App\Models\Notification[] $readNotifications
 * @property-read int|null $read_notifications_count
 * @property-read Collection|\App\Models\Report[] $reports
 * @property-read int|null $reports_count
 * @property-read Collection|\App\Models\Notification[] $unreadNotifications
 * @property-read int|null $unread_notifications_count
 * @property-read \App\Collections\PostCollection|\App\Models\Post[] $commentLikes
 * @property-read int|null $comment_likes_count
 */
class User extends JWTAuthorizeModel
{
    use HasFactory;
    use Notifiable;
    use CanResetPassword;
    use DBDefault;
    use SoftDeletes;

    public const AGE_LIMIT = 18;

    public const USER_AVATAR_PATH = 'upload/photos';
    public const USER_COVER_PATH = 'upload/photos';

    public const DEFAULT_AVATAR = 'upload/photos/d-avatar.svg';
    public const DEFAULT_COVER = 'upload/photos/d-cover.jpg';

    public const USER_STATUS_ACTIVE = '1';
    public const USER_STATUS_NOT_ACTIVE = '0';

    public const MEDIA_MIMETYPES = [
        'image/jpeg',
        'image/jpg',
        'image/png'
    ];

    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';
    public const GENDER_OTHER = 'other';

    public const GENDERS = [
        self::GENDER_MALE,
        self::GENDER_FEMALE,
        self::GENDER_OTHER,
    ];

    public const TYPE_ROLES = [
        'user',
        'admin',
        'moderator',
        'sales',
        'article_author',
        'business_developer',
    ];

    public const PRIVACY_POST_TIMELINE_EVERYONE = 'everyone';
    public const PRIVACY_POST_TIMELINE_PEOPLE_I_FOLLOW = 'ifollow';
    public const PRIVACY_POST_TIMELINE_NOBODY = 'nobody';

    public const FOLLOW_PRIVACY_EVERYONE = '0';
    public const FOLLOW_PRIVACY_PEOPLE_I_FOLLOW = '1';

    public const MESSAGE_PRIVACY_EVERYONE = '0';
    public const MESSAGE_PRIVACY_PEOPLE_I_FOLLOW = '1';
    public const MESSAGE_PRIVACY_NOBODY = '2';

    public const FRIEND_PRIVACY_EVERYONE = '0';
    public const FRIEND_PRIVACY_PEOPLE_I_FOLLOW = '1';
    public const FRIEND_PRIVACY_PEOPLE_FOLLOW_ME = '2';
    public const FRIEND_PRIVACY_NOBODY = '3';

    public const POST_PRIVACY_EVERYONE = '0';
    public const POST_PRIVACY_NOBODY = '2';
    public const POST_PRIVACY_PEOPLE_I_FOLLOW = '1';

    public const CONFIRM_FOLLOWERS_PRIVACY_NO = '0';
    public const CONFIRM_FOLLOWERS_PRIVACY_YES = '1';

    public const GROUP_CHAT_PRIVACY_NO = '0';
    public const GROUP_CHAT_PRIVACY_YES = '1';

    public const SHOW_ACTIVITIES_PRIVACY_NO = '0';
    public const SHOW_ACTIVITIES_PRIVACY_YES = '1';

    public const STATUS_PRIVACY_OFFLINE = '0';
    public const STATUS_PRIVACY_ONLINE = '1';

    public const SHARE_MY_LOCATION_PRIVACY_NO = '0';
    public const SHARE_MY_LOCATION_PRIVACY_YES = '1';
    public const SHARE_MY_DATA_PRIVACY_NO = '0';
    public const SHARE_MY_DATA_PRIVACY_YES = '1';

    public const PRIVACY_FIELDS = [
        'follow_privacy' => [
            'everyone' => self::FOLLOW_PRIVACY_EVERYONE,
            'ifollow' => self::FOLLOW_PRIVACY_PEOPLE_I_FOLLOW,
        ],
        'message_privacy' => [
            'everyone' => self::MESSAGE_PRIVACY_EVERYONE,
            'ifollow' => self::MESSAGE_PRIVACY_PEOPLE_I_FOLLOW,
            'nobody' => self::MESSAGE_PRIVACY_NOBODY,
        ],
        'friend_privacy' => [
            'everyone' => self::FRIEND_PRIVACY_EVERYONE,
            'ifollow' => self::FRIEND_PRIVACY_PEOPLE_I_FOLLOW,
            'nobody' => self::FRIEND_PRIVACY_NOBODY,
            'followme' => self::FRIEND_PRIVACY_PEOPLE_FOLLOW_ME,
        ],
        'post_privacy' => [
            'everyone' => self::POST_PRIVACY_EVERYONE,
            'ifollow' => self::POST_PRIVACY_PEOPLE_I_FOLLOW,
            'nobody' => self::POST_PRIVACY_NOBODY,
        ],
        'confirm_followers' => [
            'no' => self::CONFIRM_FOLLOWERS_PRIVACY_NO,
            'yes' => self::CONFIRM_FOLLOWERS_PRIVACY_YES,
        ],
        'group_chat_privacy' => [
            'no' => self::GROUP_CHAT_PRIVACY_NO,
            'yes' => self::GROUP_CHAT_PRIVACY_YES,
        ],
        'show_activities_privacy' => [
            'no' => self::SHOW_ACTIVITIES_PRIVACY_NO,
            'yes' => self::SHOW_ACTIVITIES_PRIVACY_YES,
        ],
        'status' => [
            'offline' => self::STATUS_PRIVACY_OFFLINE,
            'online' => self::STATUS_PRIVACY_ONLINE,
        ],
        'share_my_location' => [
            'no' => self::SHARE_MY_LOCATION_PRIVACY_NO,
            'yes' => self::SHARE_MY_LOCATION_PRIVACY_YES,
        ],
        'share_my_data' => [
            'no' => self::SHARE_MY_DATA_PRIVACY_NO,
            'yes' => self::SHARE_MY_DATA_PRIVACY_YES,
        ],
    ];

    public const PASSWORD_VALIDATION_REGEX = '/^(?=.*[[:lower:]])(?=.*[[:upper:]])(?=.*[[:digit:]]).+$/';

    public const NOTIFICATIONS_ALLOW_NO = '0';
    public const NOTIFICATIONS_ALLOW_YES = '1';

    public const NOTIFICATIONS_ALLOW = [
        'no' => self::NOTIFICATIONS_ALLOW_NO,
        'yes' => self::NOTIFICATIONS_ALLOW_YES,
    ];

    public const NOTIFICATIONS_ALLOW_FROM_EVERYONE = '0';
    public const NOTIFICATIONS_ALLOW_FROM_PEOPLE_FOLLOW_ME = '2';
    public const NOTIFICATIONS_ALLOW_FROM_ONLY_ME = '3';

    public const NOTIFICATIONS_ALLOW_FROM = [
        'everyone' => self::NOTIFICATIONS_ALLOW_FROM_EVERYONE,
        'followme' => self::NOTIFICATIONS_ALLOW_FROM_PEOPLE_FOLLOW_ME,
        'onlyme' => self::NOTIFICATIONS_ALLOW_FROM_ONLY_ME,
    ];

    public const NOTIFICATIONS_SOUNDS_OFF = '0';
    public const NOTIFICATIONS_SOUNDS_ON = '1';

    public const NOTIFICATIONS_SOUNDS = [
        'off' => self:: NOTIFICATIONS_SOUNDS_OFF,
        'on' => self::NOTIFICATIONS_SOUNDS_ON,
    ];

    protected $table = 'Wo_Users';

    protected $primaryKey = 'user_id';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'about',
        'status',
        'active',
        'admin',
        'type',
        'is_pro',
        'pro_type',
        'pro_time',
        'lastseen',
        'phone_number',
        'avatar',
        'cover',
        'birthday',
        'gender',
        'verified',
        'country_id',
        'email_code',
        'last_location_update',
        'website',
        'follow_privacy',
        'message_privacy',
        'friend_privacy',
        'post_privacy',
        'confirm_followers',
        'group_chat_privacy',
        'show_activities_privacy',
        'status',
        'share_my_location',
        'share_my_data',
        'google',
        'apple',
        'notifications_allow',
        'notifications_from',
        'notifications_sound',
    ];

    public $perPage = 10;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'country_id'
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_following' => 'boolean',
        'active' => 'boolean',
        'country_id' => 'integer',
        'admin' => 'integer',
        'created_at' => 'timestamp',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'phone_number_code'
    ];

    /**
     * @param array $models
     * @return UserCollection|Collection
     */
    public function newCollection(array $models = []): UserCollection
    {
        return UserCollection::make($models);
    }

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = []): bool
    {
        if ($this->country_iso) {
            $this->country_id = Country::getIDByISO($this->country_iso);
        }

        unset($this->country_iso);
        return parent::save($options); // TODO: Change the autogenerated stub
    }

    /**
     * @return array
     */
    public function routeNotificationForFCM(): array
    {
        return $this->FCMTokens()->pluck('device_token')->toArray();
    }

    /**
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id', 'user_id');
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'user_id', 'user_id');
    }

    /**
     * @return HasMany
     */
    public function commentReplies(): HasMany
    {
        return $this->hasMany(CommentReply::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function myGroupChats(): HasMany
    {
        return $this->hasMany(GroupChat::class, 'user_id', 'user_id');
    }

    /**
     * @param $current_user_id
     * @param $user_id
     * @return mixed
     */
    public static function getFollowers($current_user_id, $user_id): Collection
    {
        return self::select('user_id', 'username', 'first_name', 'last_name', 'avatar')
            ->withCount([
                'followers as is_following' => function ($query) use ($current_user_id) {
                    $query->where('follower_id', '=', $current_user_id)
                        ->where('active', '=', '1');
                },
                'followers',
            ])
            ->whereIn('user_id', function ($query) use ($user_id) {
                $query->select('follower_id')
                    ->from('Wo_Followers')
                    ->where('following_id', '=', $user_id);
            })
            ->where('active', '=', '1')
            ->get();
    }

    /**
     * @param $current_user_id
     * @param $user_id
     * @return mixed
     */
    public static function getFollowed($current_user_id, $user_id): Collection
    {
        return self::select('user_id', 'username', 'first_name', 'last_name', 'avatar')
            ->withCount([
                'followers as is_following' => function ($query) use ($current_user_id) {
                    $query->where('follower_id', '=', $current_user_id)
                        ->where('active', '=', '1');
                },
                'followers',
            ])
            ->whereIn('user_id', function ($query) use ($user_id) {
                $query->select('following_id')
                    ->from('Wo_Followers')
                    ->where('follower_id', '=', $user_id);
            })
            ->where('active', '=', '1')
            ->get();
    }

    /**
     * @param $current_user_id
     * @return mixed
     */
    public static function getFollowingRequests($current_user_id, $user_id): Collection
    {
        return self::select('user_id', 'username', 'first_name', 'last_name', 'avatar')
            ->withCount([
                'followers as is_following' => function ($query) use ($current_user_id) {
                    $query->where('follower_id', '=', $current_user_id)
                        ->where('active', '=', '1');
                },
                'followers',
            ])
            ->whereIn('user_id', function ($query) use ($user_id) {
                $query->select('follower_id')
                    ->from('Wo_Followers')
                    ->where('following_id', '=', $user_id)
                    ->where('active', '=', '0');
            })
            ->where('active', '=', '1')
            ->get();
    }



    /**
     * @return UserSearch
     */
    public static function search(): UserSearch
    {
        return new UserSearch();
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->admin == 1;
    }

    /**
     * @return string
     */
    public function getRoleAttribute(): string
    {
        return self::TYPE_ROLES[$this->admin];
    }

    /**
     * @return bool
     */
    public function isModerator(): bool
    {
        return $this->admin == 2;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active == 1;
    }

    /**
     * @return bool
     */
    public function isNotActive(): bool
    {
        return !$this->isActive();
    }

    /**
     * @param string $value
     * @return string|null
     */
    public function getAvatarAttribute(string $value): ?string
    {
        return $this->checkDBDefaultValue(self::DEFAULT_AVATAR, 'avatar');
    }

    /**
     * @param string $value
     * @return string|null
     */
    public function getCoverAttribute(string $value): ?string
    {
        return $this->checkDBDefaultValue(self::DEFAULT_COVER, 'cover');
    }

    /**
     * @return HasMany
     */
    public function fromMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'from_id', 'user_id');
    }

    /**
     * @return HasMany
     */
    public function toMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'to_id', 'user_id');
    }

    /**
     * Get all Group Chats for User
     * @return BelongsToMany
     */
    public function groupChats(): BelongsToMany
    {
        return $this->belongsToMany(GroupChat::class, 'Wo_GroupChatUsers', 'user_id', 'group_id');
    }

    /**
     * Followings - подписки
     * @return BelongsToMany
     */
    public function followings(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'Wo_Followers', 'follower_id', 'following_id', 'user_id', 'user_id')
            ->withTimestamps()
            ->wherePivot('active', Follower::STATUS_ACTIVE)
            ->using(Follower::class);
    }

    /**
     * @return BelongsToMany
     */
    public function followingRequests(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'Wo_Followers', 'follower_id', 'following_id', 'user_id', 'user_id')
            ->withTimestamps()
            ->wherePivot('active', Follower::STATUS_NOT_ACTIVE);
    }

    /**
     * Followers - подписчики
     * @return BelongsToMany
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'Wo_Followers', 'following_id', 'follower_id', 'user_id', 'user_id')
            ->wherePivot('active', Follower::STATUS_ACTIVE);
    }

    /**
     * @return BelongsToMany
     */
    public function followerRequests(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'Wo_Followers', 'following_id', 'follower_id', 'user_id', 'user_id')
            ->wherePivot('active', Follower::STATUS_NOT_ACTIVE);
    }

    /**
     * @return BelongsToMany
     */
    public function postLikes(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'Wo_Likes', 'user_id', 'post_id', 'user_id', 'post_id')
                    ->using(Like::class);
    }

    /**
     * @return BelongsToMany
     */
    public function postReactions(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'Wo_Reactions', 'user_id', 'post_id', 'user_id', 'post_id');
    }

    /**
     * @return BelongsToMany
     */
    public function commentLikes(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'Wo_Reactions', 'user_id', 'comment_id', 'user_id', 'comment_id');
    }

    /**
     * @return BelongsToMany
     */
    public function commentRepliesLikes(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'Wo_Reactions', 'user_id', 'replay_id', 'user_id', 'reply_id');
    }

    /**
     * @return HasMany
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'user_id', 'user_id');
    }

    /**
     * @return BelongsToMany
     */
    public function commentReports(): BelongsToMany
    {
        return $this->belongsToMany(Comment::class, 'Wo_Reports', 'user_id', 'comment_id', 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function myGroups(): HasMany
    {

        return $this->hasMany(Group::class, 'user_id', 'user_id');
    }

    /**
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'Wo_Group_Members', 'user_id', 'group_id')->using(GroupMember::class);
    }

    /**
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, "country_id", "id");
    }

    /**
     * @return HasOne
     */
    public function field(): HasOne
    {
        return $this->hasOne(UserField::class, 'user_id', 'user_id');
    }

    /**
     * @return HasMany
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'recipient_id', 'user_id');
    }

    /**
     * @return HasMany
     */
    public function readNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'recipient_id', 'user_id')
            ->where('seen', Notification::NOTIFICATION_SEEN);
    }

    /**
     * @return HasMany
     */
    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'recipient_id', 'user_id')
            ->where('seen', Notification::NOTIFICATION_UNSEEN);
    }

    /**
     * @return BelongsToMany
     */
    public function blogBookmarks(): BelongsToMany
    {
        return $this->belongsToMany(Blog::class, 'Wo_BlogsBookmarks', 'user_id', 'blog_id')->using(BlogBookmark::class);
    }

    /**
     * @return int
     */
    public function getPhoneNumberCodeAttribute(): ?int
    {
        return $this->country()->value('phonecode');
    }

    /**
     * @return HasOne
     */
    public function position(): HasOne
    {
        return $this->hasOne(UserField::class, 'user_id', 'user_id');
    }

    /**
     * @return HasMany
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Blog::class, 'user', 'user_id');
    }

    /**
     * @return HasMany
     */
    public function FCMTokens(): HasMany
    {
        return $this->hasMany(FCMToken::class, 'user_id', 'user_id');
    }

    /**
     * @return HasMany
     */
    public function authRefreshTokens(): HasMany
    {
        return $this->hasMany(AuthRefreshToken::class, 'user_id', 'user_id');
    }

    /**
     * @return BelongsToMany
     */
    public function hidePosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'Wo_HiddenPosts', 'user_id', 'post_id')->using(HidePost::class);
    }

    /**
     * @return BelongsToMany
     */
    public function pinnedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'Wo_PinnedPosts', 'user_id', 'post_id');
    }

    /**
     * @return BelongsToMany
     */
    public function postReports(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'Wo_Reports', 'user_id', 'post_id', 'user_id', 'post_id');
    }
}
