<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\Blog\Comment\BlogCommentLikedEvent;
use App\Events\Blog\CommentReply\BlogCommentReplyCreatedEvent;
use App\Events\Blog\CommentReply\BlogCommentReplyLikedEvent;
use App\Events\Follow\FollowedEvent;
use App\Events\Post\Comment\PostCommentCreatedEvent;
use App\Events\Post\PostRepostedEvent;
use App\Events\PostCommentReplyCreatedEvent;
use App\Events\Post\Comment\PostCommentLikeEvent;
use App\Events\Post\CommentReply\PostCommentReplyLikeEvent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'SocialiteProviders\\Apple\\AppleExtendSocialite@handle',
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        FollowedEvent::class => [
            \App\Listeners\Follow\FollowedListener::class
        ],

        PostRepostedEvent::class => [
            \App\Listeners\Post\PostRepostedListener::class
        ],

        #region For Blogs
        BlogCommentLikedEvent::class => [
            \App\Listeners\Blog\Comment\BlogCommentLikedListener::class
        ],
        BlogCommentReplyCreatedEvent::class => [
            \App\Listeners\Blog\CommentReply\BlogCommentReplyCreatedListener::class
        ],
        BlogCommentReplyLikedEvent::class => [
            \App\Listeners\Blog\CommentReply\BlogCommentReplyLikedListener::class
        ],
        #endregion

        PostCommentCreatedEvent::class => [
            \App\Listeners\Post\Comment\PostCommentCreatedListener::class
        ],
        PostCommentReplyCreatedEvent::class => [
            \App\Listeners\Post\CommentReply\PostCommentReplyCreatedListener::class
        ],
        PostCommentLikeEvent::class => [
            \App\Listeners\Post\Comment\PostCommentLikeListener::class
        ],
        PostCommentReplyLikeEvent::class => [
            \App\Listeners\Post\CommentReply\PostCommentReplyLikeListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
