<?php

namespace App\Providers;

use App\Models\BlogBookmark;
use App\Models\BlogComment;
use App\Models\BlogCommentReply;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\CommentReply;
use App\Models\CommentReplyLike;
use App\Models\Follower;
use App\Models\Group;
use App\Models\Like;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use App\Observers\BlogBookmarkObserver;
use App\Observers\BlogCommentObserver;
use App\Observers\BlogCommentReplyObserver;
use App\Observers\CommentLikeObserver;
use App\Observers\CommentObserver;
use App\Observers\CommentReplyLikeObserver;
use App\Observers\CommentReplyObserver;
use App\Observers\FollowerObserver;
use App\Observers\GroupObserver;
use App\Observers\LikeObserver;
use App\Observers\MessageObserver;
use App\Observers\NotificationObserver;
use App\Observers\PostObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

/**
 * class AppServiceProvider
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->app->bind('google_client', fn(): \Google_Client => new \Google_Client(['client_id' => config('services.google.client_id')]));

        $this->setProtocol();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        User::observe(UserObserver::class);
        Post::observe(PostObserver::class);
        Message::observe(MessageObserver::class);
        Group::observe(GroupObserver::class);
        BlogComment::observe(BlogCommentObserver::class);
        BlogCommentReply::observe(BlogCommentReplyObserver::class);
        BlogBookmark::observe(BlogBookmarkObserver::class);
        Comment::observe(CommentObserver::class);
        CommentLike::observe(CommentLikeObserver::class);
        Notification::observe(NotificationObserver::class);
        CommentReply::observe(CommentReplyObserver::class);
        CommentReplyLike::observe(CommentReplyLikeObserver::class);
        Like::observe(LikeObserver::class);
        Follower::observe(FollowerObserver::class);
    }

    private function setProtocol(): void
    {
        if (getenv('APP_ENV') != 'local') {
            $this->app['request']->server->set('HTTPS', true);
        }
    }
}
