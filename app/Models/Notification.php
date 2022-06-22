<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * class Notification
 * @package App\Models
 *
 * @property string $type
 */
class Notification extends Model
{
    // Statuses (type)
    public const STATUS_REACTION                = 'reaction';
    public const STATUS_FOLLOWING               = 'following';
    public const STATUS_SHARE_YOUR_POST         = 'shared_your_post';
    public const STATUS_COMMENT                 = 'comment';
    public const STATUS_COMMENT_REPLY           = 'comment_reply';
    public const STATUS_INVITED_PAGE            = 'invited_page';
    public const STATUS_INVITED_GROUP           = 'invited_group';
    public const STATUS_LINKED_PAGE             = 'liked_page';
    public const STATUS_VISITED_PROFILE         = 'visited_profile';
    public const STATUS_ACCEPT_JOIN_REQUEST     = 'accepted_join_request';
    public const STATUS_SENT_U_MONEY            = 'sent_u_money';
    public const STATUS_GROUP_ADMIN             = 'group_admin';
    public const STATUS_ACCEPTED_REQUEST        = 'accepted_request';

    public const STATUS_JOINED_GROUP            = 'joined_group';
    public const STATUS_ADDED_YOU_TO_GROUP      = 'added_you_to_group';
    public const STATUS_REQUESTED_TO_JOIN_GROUP = 'requested_to_join_group';

    // Reactions (type2)
    public const REACTION_LIKE  = 'Like';
    public const REACTION_HAHA  = 'HaHa';
    public const REACTION_WOW   = 'Wow';
    public const REACTION_LOVE  = 'Love';

    public const NOTIFICATION_SEEN = '1';
    public const NOTIFICATION_UNSEEN = '0';


    protected $table = 'Wo_Notifications';

    public $timestamps = false;

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        'notifier_id',
        'recipient_id',
        'post_id',
        'reply_id',
        'comment_id',
        'page_id',
        'group_id',
        'group_chat_id',
        'event_id',
        'thread_id',
        'blog_id',
        'story_id',
        'seen_pop',
        'type',
        'type2',
        'text',
        'url',
        'full_link',
        'seen',
        'sent_push',
        'time'
    ];

    /**
     * @return BelongsTo
     */
    public function notifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'notifier_id', 'user_id')->select('user_id', 'first_name', 'last_name', 'avatar');
    }

    /**
     * @return BelongsTo
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id', 'user_id')->select('user_id', 'first_name', 'last_name', 'avatar');
    }

    /**
     * @return BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id', 'post_id');
    }

    /**
     * @return BelongsTo
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'comment_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function reply(): BelongsTo
    {
        return $this->belongsTo(CommentReply::class, 'reply_id', 'id');
    }
}
